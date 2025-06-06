<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller 
{ 
    public function login() 
    { 
        if(Auth::check()){
            return redirect('/'); 
        } 
        return view('auth.login'); 
    } 
 
    public function postlogin(Request $request) 
    {
        if($request->ajax() || $request->wantsJson()){ 
            $credentials = $request->only('username', 'password'); 
 
            if (Auth::attempt($credentials)) { 
                return response()->json([ 
                    'status' => true, 
                    'message' => 'Login Berhasil', 
                    'redirect' => url('/') 
                ]); 
            } 
             
            return response()->json([ 
                'status' => false, 
                'message' => 'Login Gagal' 
            ]); 
        } 
 
        return redirect('login'); 
    } 
 
    public function logout(Request $request) 
    { 
        Auth::logout(); 
 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken();     
        return redirect('login'); 
    } 

    public function register() 
    {
        if(Auth::check()){
            return redirect('/'); 
        }

        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('auth.register')->with('level', $level);
    }

    public function postRegister(Request $request) 
    {
        if($request->ajax() || $request->wantsJson()){
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama'     => 'required|string|max:100',
                'password' => 'required|min:5'
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
    
            $user = UserModel::create($request->all());
            Auth::login($user);

            return response()->json([
                'status' => true,
                'message' => 'Register Berhasil'
            ]);
        }
        redirect('/');
    }
} 