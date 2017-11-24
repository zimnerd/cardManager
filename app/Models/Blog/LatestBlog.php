<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Routing\Controller;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use Encore\Admin\Facades\Admin;

class LatestBlog extends Model
{

  public function paginate()
  {
    $deleteConfirm = trans('admin.delete_confirm');
    $confirm = trans('admin.confirm');
    $cancel = trans('admin.cancel');
    $script = <<<SCRIPT

console.log("Loggedttt");
var endpoint = 'http://192.168.10.89:3000/posts';


SCRIPT;

    Admin::script($script);


    $perPage = Request::get('per_page', 10);

    $page = Request::get('page', 1);

    $start = ($page - 1) * $perPage;
    $fullData = json_decode(file_get_contents("http://192.168.10.89:3000/posts"));
    $dataJson = file_get_contents("http://192.168.10.89:3000/posts/?_page=$page&_limit=$perPage");

    $data = json_decode($dataJson, true);

    extract($data);

    $posts = static::hydrate($data);
    //dd(count($fullData));
    //dd($fullData);
    $paginator = new LengthAwarePaginator($posts, count($fullData), $perPage);

    $paginator->setPath(url()->current());

    return $paginator;
  }

  public static function with($relations)
  {
    return new static;
  }

  public function findOrFail($id)
  {
    $data = file_get_contents("http://192.168.10.89:3000/posts/$id");
    $data = json_decode($data, true);

    return static::newFromBuilder($data);
  }

  public function save(array $options = [])
  {
    $formData = $this->getAttributes();
      $client = new GuzzleHttpClient();
      if (isset($formData['id'])){
        $id = $formData['id'];
        $method = 'PUT';
      }
      else
      {
        $id = "";
        $method = 'POST';
      }

      $response = $client->request($method, 'http://192.168.10.89:3000/posts/'.$id, [
        'form_params' => [
          'userId' => Admin::user()->id,
          'title' => $formData['title'],
          'body' => $formData['body']
        ]
      ]);

    //dd($response);
  }

}