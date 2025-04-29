php artisan make:migration create_users_table --create=users

php artisan migrate // executar as migrações pendentes no banco de dados

php artisan migrate:rollback
// desfazer a última migration

php artisan migrate:refresh
// reseta e reaplica todas do zero, útil em desenvolvimento para reconstruir o esquema rapidamente.

php artisan make:model Post
// criar um model 

php artisan make:controller UserController
// Criar um controller

Route::get('user/{id}', 'UserController@show');
// Criar uma rota que chama o controller UserController e o método show
// O Laravel irá automaticamente resolver a dependência do UserController e injetá-lo na rota.


php artisan make:controller PhotoController --resource 
// Criar um controller de recurso, que irá gerar os métodos padrão para um CRUD (index, create, store, show, edit, update, destroy)
// O Laravel irá automaticamente resolver a dependência do PhotoController e injetá-lo na rota.


Fluxo MVC Resumido no Laravel: Quando uma requisição web chega:
Ela é roteada pelo Laravel através das definições em routes/web.php (ou api.php para APIs REST).
A rota, possivelmente após passar por middlewares (ver 2.6), encaminha a requisição para o método apropriado de um Controller​
.
O Controller obtém/manipula os dados necessários usando Models (Eloquent ORM) – por exemplo, busca do banco informações – e então retorna uma View (tipicamente uma página Blade) ou outra resposta (JSON, redirecionamento, etc.).
A View (o V do MVC) é renderizada, combinando um template Blade com os dados fornecidos pelo controller, e o resultado HTML é enviado de volta ao cliente.
RESUMO:
Rota -> Controller -> Model/BD -> View -> HTML/JSON -> Cliente


php artisan **make:middleware NomeMiddleware** // Criar um middleware
// Middleware é uma camada de código que pode ser executada antes ou depois de uma requisição HTTP. Eles podem ser usados para autenticação, verificação de permissões, etc.

php artisan **make:seeder NomeSeeder**  // Criar um seeder
// Seeder é uma classe que permite popular o banco de dados com dados iniciais ou de teste. Eles são úteis para preencher tabelas com dados fictícios durante o desenvolvimento ou testes.

// php artisan **make:request NomeRequest** // Criar um request
// Request é uma classe que encapsula a lógica de validação e autorização de dados de entrada. Eles são usados para validar dados antes de serem processados pelo controller.


-------------- MIDDLEWARES --------------
Route::get('perfil', 'UserController@profile')->middleware('auth'); // Rota protegida por middleware de autenticação
Route::get('admin', 'AdminController@index')->middleware('auth', 'admin'); // Rota protegida por dois middlewares: auth e admin

$this->middleware('auth'); // Aplicar o middleware de autenticação a todas as rotas do controller
$this->middleware('auth')->except(['index', 'show']); // Aplicar o middleware de autenticação a todas as rotas do controller, exceto index e show
$this->middleware('auth')->only(['create', 'store']); // Aplicar o middleware de autenticação apenas às rotas create e store do controller
$this->middleware('auth')->except('index'); // Aplicar o middleware de autenticação a todas as rotas do controller, exceto index
$this->middleware('auth')->only('edit','update'); // Aplicar o middleware de autenticação apenas às rotas edit e update do controller


--------------- RELACIONAMENTOS ---------------
Um-para-Um (One to One): Quando uma linha de uma tabela corresponde a no máximo uma linha de outra tabela. Exemplo: um Usuário tem um Perfil (tabelas users e profiles). Implementação:
Model User: public function perfil() { return $this->hasOne(Perfil::class); }
Model Perfil: public function usuario() { return $this->belongsTo(User::class); } Aqui, assumindo que profiles tem uma coluna user_id como chave estrangeira. Podemos então fazer $user->perfil para obter o objeto Perfil daquele usuário, ou $perfil->usuario para o inverso.

Um-para-Muitos (One to Many): Quando um registro de uma tabela pode ter vários associados em outra. Ex: um Usuário tem muitos Posts.
User: public function posts() { return $this->hasMany(Post::class); }
Post: public function usuario() { return $this->belongsTo(User::class); } Assim, $user->posts retorna uma coleção de posts do usuário, e $post->usuario retorna o dono do post.

Muitos-para-Muitos (Many to Many): Quando vários registros de uma tabela estão associados a vários da outra. Ex: Posts e Tags, onde um post pode ter várias tags e uma mesma tag pertence a vários posts. Esse tipo requer uma tabela pivô (ex: post_tag com post_id e tag_id).
Post: public function tags() { return $this->belongsToMany(Tag::class); }
Tag: public function posts() { return $this->belongsToMany(Post::class); } O Eloquent cuida dos detalhes, mas é preciso criar a migration da tabela pivô. Após definir, podemos fazer $post->tags (coleção) ou $tag->posts.


Além desses, Laravel suporta HasManyThrough (um-para-muitos através de um intermediário) e Relacionamentos Polimórficos, mas para começo os três acima são os principais. Por exemplo, um relacionamento um-para-um pode ser exemplificado como "um registro em uma tabela está associado a um, e somente um, em outra tabela (ex: uma pessoa e um número de documento)", um-para-muitos como "um registro está associado a vários em outra (ex: um autor e seus livros)", e muitos-para-muitos "vários registros em uma tabela associados a vários em outra (ex: alunos e cursos)"​
kinsta.com


Usando os Relacionamentos: Uma vez definidos nos models, podemos aproveitar:
Carregar relações automaticamente com eager loading: User::with('posts')->find($id) carrega o usuário e seus posts num só comando (evita N+1 query problem).
Acessar como propriedades dinâmicas: $user->posts (lista de posts), $post->usuario->nome (nome do usuário que escreveu o post).
Métodos auxiliares: $user->posts()->create([...]) cria um Post já ligado ao $user (preenche user_id automaticamente). Ou $user->posts()->save($post) para associar um post existente.
Sincronizar relações muitos-para-muitos: $post->tags()->sync([1,2,5]); atribui ao post as tags com IDs 1,2,5 removendo quaisquer outras.

DICA: // Para evitar problemas de N+1, use o eager loading (com o método with) para carregar relações antecipadamente. Isso melhora a performance ao evitar múltiplas consultas ao banco de dados. Por exemplo: $users = User::with('posts')->get(); carrega todos os usuários e seus posts em uma única consulta.
Os relacionamentos do Eloquent deixam o código mais expressivo. Em vez de escrever JOINs manualmente, você navega pelas propriedades. Por exemplo: foreach ($user->posts as $post) { echo $post->titulo; } – o Laravel cuida de buscar os posts correspondentes.


-------------------- VALIDAÇÃO DE DADOS --------------------
Validando no Controller: A classe base Controller oferece o método $this->validate($request, [...regras...]). Em Laravel 6, também podemos usar request()->validate([...]) ou $request->validate([...]). Por exemplo, em um método de controller que processa cadastro:
```php
public function store(Request $request) {
    $dados = $request->validate([
        'titulo'   => 'required|unique:posts|max:255',
        'conteudo' => 'required',
    ]);
    // Se chegar aqui, os dados são válidos.
    Post::create($dados);
    return redirect('/posts');
}

Form Request (Objeto de Validação): Para lógicas de validação mais complexas ou reutilização, podemos criar classes de request: php artisan make:request StorePostRequest. Essa classe (em app/Http/Requests) permitirá definir regras em um método rules() e aplicar autorizações. No controller, em vez de Request $request, colocar StorePostRequest $request faz com que o Laravel valide automaticamente antes de entrar no método, e se passar, já fornece um objeto request validado.

Class StorePostRequest extends FormRequest {
    public function authorize() {
        return true; // ou lógica de autorização
    }

    public function rules() {
        return [
            'titulo'   => 'required|unique:posts|max:255',
            'conteudo' => 'required',
        ];
    }
}
// No controller, o método store ficaria assim:
```php
public function store(StorePostRequest $request) {
    // Aqui, $request já é validado.
    Post::create($request->validated());
    return redirect('/posts');
}
```
```php
return response()->json($data); // Retornar resposta JSON





