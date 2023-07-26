<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function create()
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

        // Pass the variables to the view
        return view('forms.create', compact('operand1', 'operator', 'operand2'));
    }

    // Rest of your controller methods...

    public function checkCaptcha(Request $request)
    {
        $captchaResult = Session::get('captcha_result');

        $request->validate([
            'captcha_answer' => 'required|numeric'
        ]);

        $userAnswer = (int) $request->input('captcha_answer');

        if ($userAnswer === $captchaResult) {
            // Captcha passed, do something
            return redirect()->route('forms.index');
        } else {
            // Captcha failed, show an error or redirect back to the form
            return back()->withErrors(['captcha' => 'Incorrect answer']);
        }
    }
}
