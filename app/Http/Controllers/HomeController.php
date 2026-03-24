<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(): View
  {
    $viewData = [];
    $viewData['user'] = Auth::user();
    $viewData['pets'] = $viewData['user']->pets;
    
    $layout = Auth::check() && Auth::user()->role !== 'client' ? 'layouts.dashboard' : 'layouts.app';

    return view('home.index')->with('viewData', $viewData)->with('layout', $layout);
  }
}