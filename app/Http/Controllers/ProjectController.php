<?php

namespace App\Http\Controllers;

use App\Imports\ProjectsImport;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller{
    public function index(){
        $projects = Project::latest()->paginate(5);
        $id = Auth::id();
        return view('pruebasexcel', compact('projects'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function importProject(){
        Excel::import(new ProjectsImport, request()->file('file'));
        return back()->with('success', 'Project created successfully');
    }
}
