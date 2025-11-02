<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class UserController extends SearchableController
{
    const int MAX_ITEMS = 10;
    // List users (admin only)
    function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return User::orderBy('name');
    }

    /**
     * Apply where clauses for a single search word.
     * Users table doesn't have a `code` column, so search name and email.
     */
    function applyWhereToFilterByTerm(\Illuminate\Database\Eloquent\Builder $query, string $word): void
    {
      $query->where('name', 'LIKE', "%{$word}%")
          ->orWhere('email', 'LIKE', "%{$word}%")
          ->orWhere('role', 'LIKE', "%{$word}%");
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('viewAny', User::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria);

        return view('users.list', [
            'criteria' => $criteria,
            'users' => $query->paginate(self::MAX_ITEMS),
        ]);
    }

    function showCreateForm(): View
    {
    Gate::authorize('create', User::class);

        return view('users.create-form');
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
    Gate::authorize('create', User::class);

        $data = $request->getParsedBody();

        
        $user = new User();
        $user->name = $data['name'] ?? '';
        $user->email = $data['email'] ?? '';
        
        $user->password = $data['password'] ?? '';

        
        if (!empty($data['email'])) {
            $user->email = $data['email'];
        }

       
        $actor = Auth::user();
        /** @var User $actor */
        if (!empty($data['role']) && $actor && $actor->isAdministrator()) {
            $user->role = $data['role'];
        }

        try {
            $user->save();

            return redirect(
                session()->get('bookmarks.users.create-form', route('users.list'))
            )
                ->with('status', "User {$user->name} was created.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    // View self profile
    function viewSelf(): View
    {
    $user = Auth::user();
    /** @var User $user */

    Gate::authorize('view', $user);

        return view('users.view-self', [
            'user' => $user,
        ]);
    }

    function showUpdateSelfForm(): View
    {
    $user = Auth::user();
    /** @var User $user */

    Gate::authorize('update', $user);

        return view('users.update-self-form', [
            'user' => $user,
        ]);
    }

    function updateSelf(ServerRequestInterface $request): RedirectResponse
    {
    $user = Auth::user();
    /** @var User $user */

    Gate::authorize('update', $user);

        $data = $request->getParsedBody();
        // Never allow changing role via self-update
        if (isset($data['role'])) {
            unset($data['role']);
        }

        // Do not allow changing email via self-update
        if (isset($data['email'])) {
            unset($data['email']);
        }

        // Handle password: if empty or not provided, keep existing password.
        // If provided, require confirmation to match.
        if (isset($data['password'])) {
            $pw = trim((string)$data['password']);
            if ($pw === '') {
                unset($data['password']);
            } else {
                if (!isset($data['password_confirmation']) || $data['password_confirmation'] !== $data['password']) {
                    return redirect()->back()->withInput()->withErrors([
                        'password' => 'Password confirmation does not match.'
                    ]);
                }
            }
        }

        $user->fill($data);

        try {
            $user->save();

            return redirect()
                ->route('users.self.view')
                ->with('status', "Your profile was updated.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    // View any user (admin or self)
    function view(string $userId): View
    {
        $user = User::findOrFail($userId);

    Gate::authorize('view', $user);

        return view('users.view', [
            'user' => $user,
        ]);
    }

    function showUpdateForm(string $userId): View
    {
        $user = User::findOrFail($userId);

    Gate::authorize('update', $user);

        return view('users.update-form', [
            'user' => $user,
        ]);
    }

    function update(ServerRequestInterface $request, string $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);

    Gate::authorize('update', $user);

        $data = $request->getParsedBody();

        $actor = Auth::user();
        /** @var User $actor */

        // Update name if provided
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        // Update password if provided and non-empty. Require confirmation match.
        if (isset($data['password'])) {
            $pw = trim((string)$data['password']);
            if ($pw !== '') {
                if (!isset($data['password_confirmation']) || $data['password_confirmation'] !== $data['password']) {
                    return redirect()->back()->withInput()->withErrors([
                        'password' => 'Password confirmation does not match.'
                    ]);
                }
                $user->password = $pw;
            }
        }

        // Email: only allow admins to change others' emails
        if (isset($data['email']) && $actor && $actor->isAdministrator() && $actor->id !== $user->id) {
            $user->email = $data['email'];
        }

        // Role: only admins (and not changing their own role) can change role
        if (isset($data['role']) && $actor && $actor->isAdministrator() && $actor->id !== $user->id) {
            $user->role = $data['role'];
        }

        try {
            $user->save();

            return redirect()
                ->route('users.view', ['user' => $user->id])
                ->with('status', "User {$user->name} was updated.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }

    function delete(string $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);

    Gate::authorize('delete', $user);
        try {
            $user->delete();

            return redirect()
                ->route('users.list')
                ->with('status', "User {$user->name} was deleted.");
        } catch (QueryException $excp) {
            // We don't want withInput() here.
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2],
            ]);
        }
    }
}
