<?php

namespace App\Admin\Controllers\Blogs;

use App\Http\Controllers\Controller;
use App\Models\Blog\LatestBlog;
use App\Models\Blog\LatestBlogFilter;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;

class LatestBlogsController extends Controller
{
  use ModelForm;

  /**
   * Index interface.
   *
   * @return Content
   */
  public function index()
  {


    if (isset($_GET['id']))
    {
      //dd($_GET['id']);
      $postId = $_GET['id'];

      return Admin::content(function (Content $content) {

        $content->header('Posts');
        $content->description('Latest Posts');

        $content->body($this->grid());
      });
    }
    else
    {
      $postId = null;


      $script = <<<SCRIPT

console.log("Logged");
var postId = $postId
SCRIPT;

      Admin::script($script);


      return Admin::content(function (Content $content) {

        $content->header('Posts');
        $content->description('Latest Posts');

        $content->body($this->grid());
      });
    }
  }

  /**
   * Edit interface.
   *
   * @param $id
   * @return Content
   */
  public function edit($id)
  {
    return Admin::content(function (Content $content) use ($id) {

      $content->header('Posts');
      $content->description('edit');

      $content->body($this->form()->edit($id));
    });
  }

  public function search()
  {
    dd();
  }
  /**
   * Make a grid builder.
   *
   * @return Grid
   */
  /**
   * Create interface.
   *
   * @return Content
   */
  public function create()
  {
    return Admin::content(function (Content $content) {

      $content->header('header');
      $content->description('description');

      $content->body($this->form());
    });
  }

 public function grid()
  {

    return Admin::grid(LatestBlog::class, function (Grid $grid)  {

      $grid->userId()->badge();
      $grid->id()->badge();
      $grid->title();
      $grid->body();
      //$grid->setupFilter("http://192.168.10.89:3000/posts");


      $grid->filter(function (Grid\Filter $filter) {

        // $filter->equal('userId');
      });    $grid->filter(function (Grid\Filter $filter) {

        // $filter->equal('userId');
      });

      //    $grid->disableActions();
      // $grid->disableBatchDeletion();
      //      $grid->disableExport();
      //     $grid->disableCreation();
       $grid->disableFilter();

      //  $grid->resource('http://192.168.10.89:3000/posts');
    });

  }


  protected function form()
  {
    return Admin::form(LatestBlog::class, function (Form $form) {

      $form->display('id', 'ID');
      //$form->text('userId')->rules('required');
      $form->text('title');
      $form->textarea('body');

    });
  }

  public function destroy($id)
  {
    $client = new GuzzleHttpClient();
    $apiRequest = $client->request('DELETE', 'http://192.168.10.89:3000/posts/' . $id);
  }

}