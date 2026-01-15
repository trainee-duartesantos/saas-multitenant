<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    /**
     * Lista projetos do tenant atual
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        return Inertia::render('Projects/Index', [
            'projects' => Project::all(),
        ]);
    }

    /**
     * Criar novo projeto
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        Project::create(
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ])
        );

        return redirect()->back();
    }

    /**
     * Apagar projeto
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->back();
    }
}
