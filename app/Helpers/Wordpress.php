<?php
namespace App\Helpers;

use App\Categorias;
use App\Materias;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Word
{
    private $guzzle;
    private $base_rest;
    private $url;
    private $total_post;
    private $total_pagina;
    private $resultado;
    private $token_wp;
    private $payload;
    private $user_padrao;
    private $password_padrao;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
        $this->user_padrao = 'unkcreww';
        $this->password_padrao = 'g9a4b7u5';

        $this->token_wp = base64_encode($this->user_padrao . ':' . $this->password_padrao);
    }

    public function dominio($dominio)
    {
        $this->base_rest = $dominio . "/wp-json/wp/v2";
    }

    public function criar_usuario($data)
    {
        $this->url = $this->base_rest . "/users";
        $this->payload = $data;
        $this->acessar_post();

        return $this->resultado;
    }

    public function criar_post($data)
    {
        $this->url = $this->base_rest . "/posts";
        $this->payload = $data;
        $this->acessar_post();

        return $this->resultado;
    }

    public function editar_post($post_id, $data)
    {
        $this->url = $this->base_rest . "/posts/{$post_id}";
        $this->payload = $data;
        $this->acessar_post();

        $post = $this->resultado;

        return [
            'titulo' => $post->title->rendered,
            'imagem' => $this->pegar_imagem($post->featured_media),
            'post_slug' => $post->slug,
            'post_url' => $post->link,
            'post_cat' => current($post->categories),
            'post_id' => $post->id,
            'dominio' => parse_url($this->base_rest)['host'],
            'importada' => 'sim',
        ];
    }

    public function importar_post($post_id)
    {
        $this->url = $this->base_rest . "/posts/{$post_id}";
        $this->acessar();

        $post = $this->resultado;

        if ($post->status === 'publish') {
            return [
                'titulo' => $post->title->rendered,
                'imagem' => $this->pegar_imagem($post->featured_media),
                'post_slug' => $post->slug,
                'post_url' => $post->link,
                'post_cat' => current($post->categories),
                'post_id' => $post->id,
                'dominio' => parse_url($this->base_rest)['host'],
                'importada' => 'sim',
            ];
        }
    }

    public function copiar_todos_post()
    {
        $this->copiar_todas_categoria();
        $this->url = $this->base_rest . '/posts';
        $this->acessar();

        $posts_data = [];

        foreach (range(1, ceil($this->total_post / 100)) as $pagina) {
            $this->url = $this->base_rest . "/posts?_embed&per_page=100&page={$pagina}";
            $this->acessar();

            foreach ($this->resultado as $post) {
                if ($post->status === 'publish') {
                    try {
                        $dominio = parse_url($this->base_rest)['host'];

                        $materia = Materias::where('dominio', $dominio)
                            ->where('post_id', $post->id)
                            ->first();

                        if (!empty($materia)) {
                            Materias::where('id', $materia->id)->update([
                                'titulo' => $post->title->rendered,
                                'imagem' => $this->pegar_imagem($post->featured_media),
                                'post_slug' => $post->slug,
                                'post_url' => $post->link,
                                'post_cat' => $this->id_categoria(current($post->categories), $dominio)->id,
                                'post_id' => $post->id,
                                'dominio' => $dominio,
                                'importada' => 'sim',
                            ]);
                        } else {
                            Materias::create([
                                'titulo' => $post->title->rendered,
                                'imagem' => $this->pegar_imagem($post->featured_media),
                                'post_slug' => $post->slug,
                                'post_url' => $post->link,
                                'post_cat' => $this->id_categoria(current($post->categories), $dominio)->id,
                                'post_id' => $post->id,
                                'dominio' => $dominio,
                                'importada' => 'sim',
                            ]);
                        }
                    } catch (\Illuminate\Database\QueryException $e) {
                    }
                }
            }
        }

        return $posts_data;
    }

    public function id_categoria($post_cat, $dominio)
    {
        return Categorias::where('dominio_categoria', $dominio)
            ->where('id_categoria', $post_cat)
            ->first();
    }

    public function copiar_todas_categoria()
    {
        $this->url = $this->base_rest . "/categories?page=1&per_page=100";
        $this->acessar();
        $data = [];
        foreach ($this->resultado as $cat) {
            try {
                $categori = Categorias::where('nome_categoria', $cat->name)
                    ->where('dominio_categoria', parse_url($this->base_rest)['host'])
                    ->first();

                if (!$categori) {
                    $data[] = Categorias::create([
                        'id_categoria' => $cat->id,
                        'nome_categoria' => $cat->name,
                        'dominio_categoria' => parse_url($this->base_rest)['host'],
                        'valor_cpm' => '0.00',
                    ]);
                }
            } catch (\Illuminate\Database\QueryException $e) {
            }
        }

        return $data;
    }

    public function pegar_imagem($id)
    {
        $this->url = $this->base_rest . "/media/{$id}";
        $this->acessar();

        $arquivo = $this->resultado;

        if (!empty($arquivo->guid)) {
            return $arquivo->guid->rendered;
        }
    }

    public function carregar_imagem($arquivo)
    {
        $this->payload = $arquivo;

        $this->url = $this->base_rest . "/media";
        $this->fazer_upload();

        return $this->resultado;
    }

    private function acessar()
    {
        $request = $this->guzzle->request('GET', $this->url, [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Basic ' . $this->token_wp,
            ],
        ]);

        $this->resultado = json_decode($request->getBody()->getContents());

        $this->total_pagina = (int) $request->getHeaderLine('X-WP-TotalPages');

        $this->total_post = (int) $request->getHeaderLine('X-WP-Total');

        if ((int) $request->getStatusCode() == 200) {
            return true;
        }

        return false;
    }

    private function acessar_post()
    {
        $request = $this->guzzle->request('POST', $this->url, [
            'headers' => [
                'Authorization' => 'Basic ' . $this->token_wp,
                'Cache-Control' => 'no-cache',
                "Content-Type" => "application/x-www-form-urlencoded",
                "Accept" => 'application/json',
            ],
            'form_params' => $this->payload,
        ]);

        $this->resultado = json_decode($request->getBody()->getContents());
    }

    private function fazer_upload()
    {
        $request = $this->guzzle->request('POST', $this->url, [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Basic ' . $this->token_wp,
            ],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => file_get_contents($this->payload),
                    'filename' => basename($this->payload),
                ],

            ],
        ]);

        $this->resultado = json_decode($request->getBody()->getContents());
    }
}
