<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IndicadosController extends Controller
{
    private $request;
    private $response;
    private $cookie;

    public function __construct(Request $request, CookieJar $cookie, Response $response)
    {
        $this->request = $request;
        $this->cookie = $cookie;
        $this->response = $response;
    }

    public function index($id)
    {
        $this->cookie->queue(cookie('ref', $id, 45000));

        // dd($this->request->cookie('ref'));

        return redirect('/');
    }
}
