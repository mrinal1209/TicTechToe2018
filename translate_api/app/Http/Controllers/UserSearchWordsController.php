<?php

namespace App\Http\Controllers;

use App\UserSearchWords;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Google\Cloud\Translate\TranslateClient;


class UserSearchWordsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $translate = new TranslateClient([]);
      $text = filter_var($request->text, FILTER_SANITIZE_STRING);
      if($text == null || preg_match('#[0-9]#',$text) || ctype_space($text))
          return response(['error' => 'Send only one correct word'],404);
      else{
      $lang_code = $translate->detectLanguage($text, [])['languageCode'];
      if($lang_code == "en"){
        if(strpos(trim($text), ' ') != false)
        {
          return response([
            'error' => 'Send only one correct word'
          ],404);
        }
        else
        {
           $content = file_get_contents('http://127.0.0.1:3001/?define='.$text.'&lang=en');
           $content = json_decode($content, true);

           return response([
              'text' => $text,
              'definition' => $content['meaning'][key($content['meaning'])][0]['definition'],
              'example' => $content['meaning'][key($content['meaning'])][0]['example']
             ],200);
        }
      }
      else{
        return response([
          'text' => $text,
          'definition' => $translate->translate($text)['text']
          // 'language_code' => $lang_code
        ],200);
      }

    }
  }


}
