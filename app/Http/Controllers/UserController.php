<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Criptografa a senha
use Illuminate\Validation\Rules; // Adiciona regras de senha
use Illuminate\Validation\Rule;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Busca os usuários do banco de dados, com paginação de 10 por página.
        $users = User::paginate(10);

        // 2. Retorna a view, passando a variável 'users' para ela.
        return view('users.index', ['users' => $users]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Busca todos os cargos para popular o select no formulário
        $roles = Role::all();
        return view('users.create', ['roles' => $roles]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validação dos dados do formulário
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'], // Valida que o cargo existe
        ]);

        // 2. Criação do usuário no banco de dados
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Criptografa a senha
        ]);

        // 3. Atribuição do cargo (role) selecionado
        $user->assignRole($request->role);

        // 4. Redirecionamento de volta para a lista com mensagem de sucesso
        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // A "mágica" do Laravel (Route Model Binding) já nos entrega o usuário correto!
        // Agora só precisamos buscar todos os cargos para popular o select.
        $roles = Role::all();

        return view('users.edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /*public function update(Request $request, User $user)
    {
        // 1. Validação dos dados (com uma regra especial para o email)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // 2. Atualiza os dados principais do usuário
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // 3. Atualiza a senha APENAS se uma nova foi digitada no formulário
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // 4. Sincroniza o cargo (a forma correta de atualizar para evitar duplicados)
        $user->syncRoles($request->role);

        // 5. Redireciona de volta para a lista com a mensagem de sucesso
        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }*/

    public function update(Request $request, User $user)
    {
        // 1. Validação
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Captura o nome do cargo ANTES da alteração
        $oldRole = $user->getRoleNames()->implode(', ');
        $newRole = $request->role;

        // Atualiza os dados do usuário (nome, email, senha)
        $user->fill($request->only(['name', 'email']));
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save(); // Isso já deve criar a auditoria para nome/email, se houver mudança

        // Sincroniza o cargo
        $user->syncRoles($newRole);

        // ================== CRIAÇÃO MANUAL DA AUDITORIA DO CARGO ==================
        // Se o cargo realmente mudou, nós criamos o registro na mão
        if ($oldRole !== $newRole) {
            Audit::create([
                'user_id' => Auth::id(), // O usuário que está fazendo a ação
                'user_type' => User::class, 
                'event' => 'updated',
                'auditable_type' => User::class,
                'auditable_id' => $user->id, // O usuário que está sendo modificado
                'old_values' => ['role' => $oldRole],
                'new_values' => ['role' => $newRole],
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
        // ==========================================================================

        // Redireciona com a mensagem de sucesso
        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Regra de segurança: impede que o usuário logado se autoexclua.
        if (auth()->id() == $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
