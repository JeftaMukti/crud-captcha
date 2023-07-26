<?php

namespace App\Http\Controllers;

use App\Models\form;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $forms = form::latest()->paginate(5);

        return view('forms.index',compact('forms'))
        ->with('i',(request()->input('page', 1 ) -1 ) *5 );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $captchaData = $this->generateCaptcha();

        return view('forms.create', $captchaData);
    }

    /**
     * Generate a random captcha.
     *
     * @return array
     */
    private function generateCaptcha()
    {
        $operators = ['+', '-', '*', '/'];
        $operator = $operators[array_rand($operators)];
        $operand1 = rand(1, 10);
        $operand2 = rand(1, 10);

        switch ($operator) {
            case '+':
                $result = $operand1 + $operand2;
                break;
            case '-':
                $result = $operand1 - $operand2;
                break;
            case '*':
                $result = $operand1 * $operand2;
                break;
            case '/':
                // Make sure the division result is an integer for simplicity
                $result = $operand1 * $operand2;
                $operand1 = $result;
                break;
        }

        // Store the result in the session
        Session::put('captcha_result', $result);

        // Return an array of data
        return compact('operand1', 'operator', 'operand2');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'website' => 'required',
            'message' => 'required',
            'captcha_answer' => 'required|numeric'
        ]);

        $captchaResult = Session::get('captcha_result');
        $userAnswer = (int) $request->input('captcha_answer');

        if ($userAnswer !== $captchaResult) {
            return back()->withErrors(['captcha' => 'salah menjawab'])->withInput();    
        }

        Form::create($request->all());
        return redirect()->route('forms.index')
        ->with('success','form berhasil dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\form  $form
     * @return \Illuminate\Http\Response
     */
    public function show(form $form)
    {
        //
        return view('forms.show',compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\form  $form
     * @return \Illuminate\Http\Response
     */
    public function edit(form $form)
    {
        $captchaData = $this->generateCaptcha();
        return view('forms.edit', compact('form') + $captchaData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\form  $form
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, form $form)
    {
        //
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'website' => 'required',
            'message' => 'required'
        ]);

        $captchaResult = Session::get('captcha_result');
        $request->validate([
            'captcha_answer' => 'required|numeric'
        ]);
        $userAnswer = (int) $request->input('captcha_answer');
        if ($userAnswer !== $captchaResult) {
            return back()->withErrors(['captcha' => 'captcha salah'])->withInput($request->except('captcha_answer'));
        }

        $form->update($request->all());
        return redirect()->route('forms.index')
        ->with('success','data berhasil di ubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\form  $form
     * @return \Illuminate\Http\Response
     */
    public function destroy(form $form)
    {
        //
        $form->delete();
        return redirect()->route('forms.index')
        ->with('success','data berhasil di hapus');
    }

}
