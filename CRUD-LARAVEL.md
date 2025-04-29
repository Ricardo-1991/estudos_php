// FLUXO DE CRIAÇÃO DE UM CRUD NO LARAVEL:
// 1. Criar a migration: php artisan make:migration create_posts_table --create=posts
```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosTable extends Migration
{
    /**
     * 1. Método que define o esquema da tabela ao rodar php artisan migrate
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');       
            // ↑ Cria coluna 'id' BIGINT auto-increment (chave primária)

            $table->string('name');             
            // ↑ Coluna 'name' VARCHAR(255) (nome do usuário)

            $table->string('email')->unique();  
            // ↑ Coluna 'email' VARCHAR(255) e índice UNIQUE para não repetir e-mails

            $table->string('password');         
            // ↑ Coluna 'password' VARCHAR(255) para senha (hash)

            $table->timestamps();               
            // ↑ Cria 'created_at' e 'updated_at' (DATETIME) automaticamente
        });
    }

    /**
     * 2. Método que desfaz a migration (drop da tabela)
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
```php	

Onde rodar: php artisan migrate
Variáveis geradas: nenhuma, apenas a tabela física no banco.

Model — representa a tabela e habilita Eloquent
```php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    // 3. Nome da tabela (omita se for plural de 'Usuario')
    protected $table = 'usuarios';

    // 4. Colunas que podem ser preenchidas em massa (mass assignment)
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // 5. Campos que devem ficar ocultos quando converter para JSON/array
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 6. Mutator: antes de salvar, criptografa a senha automaticamente
     */
    public function setPasswordAttribute($value)
    {
        // $this->attributes é o array interno de colunas do model
        $this->attributes['password'] = bcrypt($value);
    }
}
```php
De onde vem $fillable? É um array de strings no Model, não de uma variável externa.

Método setPasswordAttribute roda sempre que você fizer $usuario->password = 'texto';.


3. Controller — lógica de CRUD com comentários

```php
<?php

namespace App\Http\Controllers;

use App\Usuario;               // 7. Importa o Model Usuario
use Illuminate\Http\Request;   // 8. Classe que encapsula a requisição HTTP

class UsuarioController extends Controller
{
    /**
     * 9. GET /usuarios
     *    Exibe a lista de usuários
     */
    public function index()
    {
        $usuarios = Usuario::all();
        // ↑ Eloquent busca todos os registros da tabela 'usuarios'
        //    e retorna uma Collection de objetos Usuario

        // repassa $usuarios para a view resources/views/usuarios/index.blade.php
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * 10. GET /usuarios/create
     *     Exibe o formulário em branco para criar um novo usuário
     */
    public function create()
    {
        // não há dados pré-carregados, só renderiza o form
        return view('usuarios.create');
    }

    /**
     * 11. POST /usuarios
     *     Processa o envio do formulário de criação
     */
    public function store(Request $request)
    {
        // 12. Valida os dados obrigatórios
        $request->validate([
            'name'     => 'required|max:255',
            'email'    => 'required|email|unique:usuarios',
            'password' => 'required|min:6',
        ]);

        // 13. Cria o registro no banco usando Mass Assignment
        //    $request->only pega só os campos name, email e password
        Usuario::create($request->only(['name', 'email', 'password']));

        // 14. Redireciona para a rota nomeada 'usuarios.index' (index)
        //    e joga uma mensagem de sucesso na session
        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * 15. GET /usuarios/{usuario}
     *     Exibe detalhes de um usuário específico
     *     O Laravel já injeta em $usuario o objeto buscado por ID (Route-Model Binding)
     */
    public function show(Usuario $usuario)
    {
        // passa o objeto Usuario para a view 'usuarios.show'
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * 16. GET /usuarios/{usuario}/edit
     *     Exibe o formulário de edição já preenchido
     */
    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * 17. PUT/PATCH /usuarios/{usuario}
     *     Atualiza os dados no banco
     */
    public function update(Request $request, Usuario $usuario)
    {
        // 18. Validação: o email deve ser único, exceto para o próprio registro
        $request->validate([
            'name'  => 'required|max:255',
            'email' => 'required|email|unique:usuarios,email,'.$usuario->id,
        ]);

        // 19. Chama o método update (fornecido pelo Eloquent Model)
        //    passando só name e email. A senha não é alterada aqui.
        $usuario->update($request->only(['name', 'email']));

        // 20. Redireciona de volta para a lista com mensagem
        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário atualizado!');
    }

    /**
     * 21. DELETE /usuarios/{usuario}
     *     Remove o registro do banco
     */
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();  // 22. Remove o usuário

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário removido!');
    }
}
```php

4. Rotas — mapeando URLs para métodos do Controller

```php
    use Illuminate\Support\Facades\Route;

    // 23. Registra as 7 rotas RESTful de forma automática
    Route::resource('usuarios', 'UsuarioController');
    
```
Isso cria:

HTTP	URI	Controller@Método	Nome da Rota
GET	/usuarios	index	usuarios.index
GET	/usuarios/create	create	usuarios.create
POST	/usuarios	store	usuarios.store
GET	/usuarios/{usuario}	show	usuarios.show
GET	/usuarios/{usuario}/edit	edit	usuarios.edit
PUT/PATCH	/usuarios/{usuario}	update	usuarios.update
DELETE	/usuarios/{usuario}	destroy	usuarios.destroy


5. Views Blade — de onde vêm as variáveis e o que exibem
5.1 resources/views/usuarios/index.blade.php

```html
@extends('layouts.app')

@section('content')
  <h1>Lista de Usuários</h1>

  {{-- 24. Exibe mensagem de sucesso, se existir na sessão --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- 25. Link para criar novo usuário --}}
  <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Novo Usuário</a>

  {{-- 26. Laço sobre a Collection $usuarios (passada pelo controller) --}}
  <table class="table mt-3">
    <thead>
      <tr><th>ID</th><th>Nome</th><th>Email</th><th>Ações</th></tr>
    </thead>
    <tbody>
      @foreach($usuarios as $u)
        <tr>
          <td>{{ $u->id }}</td>       {{-- atributo 'id' do objeto Usuario --}}
          <td>{{ $u->name }}</td>     {{-- atributo 'name' --}}
          <td>{{ $u->email }}</td>    {{-- atributo 'email' --}}
          <td>
            <a href="{{ route('usuarios.show', $u) }}" class="btn btn-info btn-sm">Ver</a>
            <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-warning btn-sm">Editar</a>
            <form action="{{ route('usuarios.destroy', $u) }}" method="POST" style="display:inline">
              @csrf
              @method('DELETE')
              <button class="btn btn-danger btn-sm">Excluir</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
```html 

$usuarios vem do compact('usuarios') no método index() do Controller.

Dentro do @foreach, cada $u é um objeto Usuario, com propriedades mapeadas das colunas da tabela.

5.2 resources/views/usuarios/create.blade.php

```html
    @extends('layouts.app')

@section('content')
  <h1>Criar Usuário</h1>

  {{-- 27. Formulário aponta para a rota usuarios.store (POST /usuarios) --}}
  <form action="{{ route('usuarios.store') }}" method="POST">
    @csrf {{-- 28. Gera <input type="hidden" name="_token" ...> para CSRF --}}
    
    {{-- 29. Partial com campos name, email e password --}}
    @include('usuarios._form')
    
    <button class="btn btn-success">Salvar</button>
  </form>
@endsection
```html]
CSRF: obrigatório em forms POST/PUT/DELETE — o middleware VerifyCsrfToken exige esse token.

5.3 resources/views/usuarios/_form.blade.php (partial)

```html 
    {{-- Recebe opcionalmente $usuario quando em edit --}}
<div class="form-group">
  <label>Nome</label>
  <input type="text"
         name="name"
         value="{{ old('name', $usuario->name ?? '') }}"
         class="form-control @error('name') is-invalid @enderror">
  @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="form-group">
  <label>Email</label>
  <input type="email"
         name="email"
         value="{{ old('email', $usuario->email ?? '') }}"
         class="form-control @error('email') is-invalid @enderror">
  @error('email')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

{{-- Apenas em create mostramos senha --}}
@if(Route::is('usuarios.create'))
  <div class="form-group">
    <label>Senha</label>
    <input type="password"
           name="password"
           class="form-control @error('password') is-invalid @enderror">
    @error('password')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
@endif
```html
old('field', $usuario->field ?? '') traz o valor digitado anteriormente em caso de erro de validação, ou dados do usuário em edição.

@error('field') exibe a mensagem de erro de validação para aquele campo.

5.4 resources/views/usuarios/edit.blade.php

```html
    @extends('layouts.app')

@section('content')
  <h1>Editar Usuário</h1>

  {{-- Formulário para atualizar via PUT --}}
  <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
    @csrf
    @method('PUT') {{-- 30. “Spoofing” para enviar PUT via POST --}}
    
    @include('usuarios._form') {{-- reaproveita o partial --}}
    
    <button class="btn btn-success">Atualizar</button>
  </form>
@endsection
```html
usuario vem do edit(Usuario $usuario) do Controller.

@method('PUT') instrui o Laravel a tratar como PUT.

5.5 resources/views/usuarios/show.blade.php
```html
    @extends('layouts.app')

    @section('content')
    <h1>Usuário #{{ $usuario->id }}</h1>
    <p><strong>Nome:</strong> {{ $usuario->name }}</p>
    <p><strong>Email:</strong> {{ $usuario->email }}</p>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Voltar</a>
    @endsection
```html	


Recapitulando o ciclo
Migration cria a tabela física.

Model (Usuario) mapeia essa tabela e supre métodos como create, update, delete.

Routes (Route::resource) ligam URIs a métodos do UsuarioController.

Controller carrega dados via Eloquent e escolhe qual view renderizar, passando variáveis.

Views Blade exibem formulários e listas, usando as variáveis injetadas pelo Controller.

Cada variável nas views ($usuarios, $usuario) foi definida no Controller e passada via view(..., compact(...)). Os métodos como update() e delete() vêm da classe base Illuminate\Database\Eloquent\Model que o Usuario estende.

Dessa forma você tem todo o caminho do CRUD, explicado passo a passo!