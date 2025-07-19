<?php

namespace Plugins\ExamplePlugin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('example-plugin::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('example-plugin::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Store logic here
        return redirect()->route('example-plugin.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('example-plugin::show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('example-plugin::edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Update logic here
        return redirect()->route('example-plugin.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Delete logic here
        return redirect()->route('example-plugin.index');
    }
}
