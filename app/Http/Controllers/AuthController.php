<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use App\Mail\BemVindoMail;

use App\Models\Codigo2FA;
use App\Mail\CodigoVerificacao;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    public function register(Request $request)
    {
       $dados = $request->validate([
            'nome_advg' => 'required|string|max:100',
            'oab_advg' => 'required|string|max:20|unique:users,oab_advg',
            'email_advg' => 'required|email|max:255|unique:users,email_advg',
            'cpf_advg' => 'required|string|max:14|unique:users,cpf_advg',
            'telefone_advg' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'nome_advg' => $dados['nome_advg'],
            'oab_advg' => $dados['oab_advg'],
            'email_advg' => $dados['email_advg'],
            'cpf_advg' => $dados['cpf_advg'],
            'telefone_advg' => $dados['telefone_advg'],
            'password' => Hash::make($dados['password']),
        ]);
        // Enviar email de boas-vindas
        Mail::to($dados['email_advg'])
            ->send(new BemVindoMail($dados['nome_advg']));

        return redirect()->route('home')
            ->with('sucesso', 'Produto criado com sucesso!');
    }


    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email_advg' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $usuario = Auth::user();

            // Envia o código de autenticação
            Auth::logout();

            return $this->enviarCodigo2FA($usuario);
        }

        //if (Auth::attempt($credentials)) {
            //$request->session()->regenerate();

            //return $this->enviarCodigo2FA(Auth::user());
            //return redirect()
            //->route('processos.index')
            //->with('sucesso', 'Login efetuado com sucesso!');

        //}

        return back()
            ->withErrors([
                'email_advg' => 'E-mail ou senha incorretos.'
            ])
            ->onlyInput('email_advg');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function enviarCodigo2FA($usuario)
    {
        // Gera código de 6 dígitos
        $codigo = (string) random_int(100000, 999999);

        // Invalida códigos anteriores
        Codigo2FA::where('usuario_id', $usuario->id)
            ->where('used', false)
            ->update(['used' => true]);

        // Salva o hash do código
        Codigo2FA::create([
            'usuario_id' => $usuario->id,
            'codigo_hash' => Hash::make($codigo),
            'expira_em' => now()->addMinutes(5),
            'used' => false
        ]);

        // Guarda o usuário na sessão, sem autenticar ainda
        session([
            'usuario_2fa_id' => $usuario->id
        ]);

        // Envia o e-mail
        Mail::to($usuario->email_advg)
            ->send(new CodigoVerificacao($codigo));

        return redirect()->route('2fa.form')
            ->with('success', 'Código enviado para seu e-mail!');
    }

    public function verificarCodigo2FA(Request $request)
    {
        $request->validate([
            'codigo' => 'required|digits:6'
        ]);

        $usuarioId = session('usuario_2fa_id');

        if (!$usuarioId) {
            return redirect()->route('login');
        }

        $registro = Codigo2FA::where('usuario_id', $usuarioId)
            ->where('used', false)
            ->where('expira_em', '>', now())
            ->latest('id')
            ->first();

        if (!$registro || !Hash::check(
            $request->codigo,
            $registro->codigo_hash
        )) {
            return back()->withErrors([
                'codigo' => 'Código inválido ou expirado.'
            ]);
        }

        // Marca o código como utilizado
        $registro->update(['used' => true]);

        // Recupera o usuário
        $usuario = \App\Models\User::findOrFail($usuarioId);

        // Finalmente libera o acesso
        Auth::login($usuario);

        session()->forget('usuario_2fa_id');

        return redirect()->intended('/dashboard');
    }
}
