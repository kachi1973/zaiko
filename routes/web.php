<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/home', function () {
    return redirect(route('shukko.list'));
});

Route::any('/ajax/AjaxGetBumons',                       'App\Http\Controllers\AjaxController@AjaxGetBumons')->name('ajax.AjaxGetBumons');
Route::any('/ajax/AjaxGetUsers',                        'App\Http\Controllers\AjaxController@AjaxGetUsers')->name('ajax.AjaxGetUsers');
Route::any('/ajax/AjaxGetUserName',                     'App\Http\Controllers\AjaxController@AjaxGetUserName')->name('ajax.AjaxGetUserName');
Route::get('/ajax/GetHanko',                            'App\Http\Controllers\AjaxController@GetHanko')->name('ajax.GetHanko');
Route::any('/ajax/AjaxGetHinmoku',                      'App\Http\Controllers\AjaxController@AjaxGetHinmoku')->name('ajax.AjaxGetHinmoku');
Route::any('/ajax/AjaxGetScawHinmoku',                  'App\Http\Controllers\AjaxController@AjaxGetScawHinmoku')->name('ajax.AjaxGetScawHinmoku');
Route::any('/ajax/AjaxGetKzaiko',                       'App\Http\Controllers\AjaxController@AjaxGetKzaiko')->name('ajax.AjaxGetKzaiko');
Route::any('/ajax/AjaxJigyosyos',                       'App\Http\Controllers\AjaxController@AjaxJigyosyos')->name('ajax.AjaxJigyosyos');

Route::get('/shukko/create',                            'App\Http\Controllers\ShukkoController@main')->name('shukko.create');
Route::get('/shukko/list/{type?}',                      'App\Http\Controllers\ShukkoController@main')->name('shukko.list');
Route::get('/shukko/listredirect1',function () {
    return redirect(route('shukko.list', ['type' => 3]));
})->name('shukko.listredirect1');
Route::get('/shukko/listredirect2',function () {
    return redirect(route('shukko.list', ['type' => 4]));
})->name('shukko.listredirect2');
Route::get('/shukko/edit/{id}',                         'App\Http\Controllers\ShukkoController@main')->name('shukko.edit');
Route::get('/shukko/desc/{id}',                         'App\Http\Controllers\ShukkoController@main')->name('shukko.desc');
Route::get('/shukko/descprint/{id}',                    'App\Http\Controllers\ShukkoController@descprint')->name('shukko.descprint');
Route::any('/shukko/AjaxInit',                          'App\Http\Controllers\ShukkoController@AjaxInit')->name('shukko.AjaxInit');
Route::any('/shukko/AjaxGetZaikos',                     'App\Http\Controllers\ShukkoController@AjaxGetZaikos')->name('shukko.AjaxGetZaikos');
Route::any('/shukko/AjaxGetSZaikos',                    'App\Http\Controllers\ShukkoController@AjaxGetSZaikos')->name('shukko.AjaxGetSZaikos');
Route::any('/shukko/AjaxPutShukko',                     'App\Http\Controllers\ShukkoController@AjaxPutShukko')->name('shukko.AjaxPutShukko');
Route::any('/shukko/AjaxPutShukko2',                    'App\Http\Controllers\ShukkoController@AjaxPutShukko2')->name('shukko.AjaxPutShukko2');
Route::any('/shukko/AjaxPutFile/{id}',                  'App\Http\Controllers\ShukkoController@AjaxPutFile');
Route::any('/shukko/AjaxDeleteFile/{id}/{name}',        'App\Http\Controllers\ShukkoController@AjaxDeleteFile');
Route::any('/shukko/AjaxGetShukkos',                    'App\Http\Controllers\ShukkoController@AjaxGetShukkos')->name('shukko.AjaxGetShukkos');
Route::any('/shukko/AjaxGetShukko',                     'App\Http\Controllers\ShukkoController@AjaxGetShukko')->name('shukko.AjaxGetShukko');

Route::get('/joren/create',                             'App\Http\Controllers\JorenController@main')->name('joren.create');
Route::get('/joren/list',                               'App\Http\Controllers\JorenController@main')->name('joren.list');
Route::get('/joren/edit/{id}',                          'App\Http\Controllers\JorenController@main')->name('joren.edit');
Route::get('/joren/descprint/{id}',                     'App\Http\Controllers\JorenController@descprint')->name('joren.descprint');
Route::any('/joren/AjaxInit',                           'App\Http\Controllers\JorenController@AjaxInit')->name('joren.AjaxInit');
Route::any('/joren/AjaxGetJorens',                      'App\Http\Controllers\JorenController@AjaxGetJorens')->name('joren.AjaxGetJorens');
Route::any('/joren/AjaxGetJoren',                       'App\Http\Controllers\JorenController@AjaxGetJoren')->name('joren.AjaxGetJoren');
Route::any('/joren/AjaxPutJoren',                       'App\Http\Controllers\JorenController@AjaxPutJoren')->name('joren.AjaxPutJoren');
Route::any('/joren/AjaxPutFile/{id}',                   'App\Http\Controllers\JorenController@AjaxPutFile');
Route::any('/joren/AjaxDeleteFile/{id}/{name}',         'App\Http\Controllers\JorenController@AjaxDeleteFile');
Route::any('/joren/AjaxGetShukkos',                     'App\Http\Controllers\JorenController@AjaxGetShukkos')->name('joren.AjaxGetShukkos');

Route::get('/konyu/create',                             'App\Http\Controllers\KonyuController@main')->name('konyu.create');
Route::get('/konyu/list',                               'App\Http\Controllers\KonyuController@main')->name('konyu.list');
Route::get('/konyu/edit/{id}',                          'App\Http\Controllers\KonyuController@main')->name('konyu.edit');
Route::get('/konyu/descprint/{id}',                     'App\Http\Controllers\KonyuController@descprint')->name('konyu.descprint');
Route::any('/konyu/AjaxInit',                           'App\Http\Controllers\KonyuController@AjaxInit')->name('konyu.AjaxInit');
Route::any('/konyu/AjaxGetKonyus',                      'App\Http\Controllers\KonyuController@AjaxGetKonyus')->name('konyu.AjaxGetKonyus');
Route::any('/konyu/AjaxGetKonyu',                       'App\Http\Controllers\KonyuController@AjaxGetKonyu')->name('konyu.AjaxGetKonyu');
Route::any('/konyu/AjaxPutKonyu',                       'App\Http\Controllers\KonyuController@AjaxPutKonyu')->name('konyu.AjaxPutKonyu');
Route::any('/konyu/AjaxPutFile/{id}',                   'App\Http\Controllers\KonyuController@AjaxPutFile');
Route::any('/konyu/AjaxDeleteFile/{id}/{name}',         'App\Http\Controllers\KonyuController@AjaxDeleteFile');

Route::get('/hasei/create',                             'App\Http\Controllers\HaseiController@main')->name('hasei.create');
Route::get('/hasei/list',                               'App\Http\Controllers\HaseiController@main')->name('hasei.list');
Route::get('/hasei/edit/{id}',                          'App\Http\Controllers\HaseiController@main')->name('hasei.edit');
Route::get('/hasei/descprint/{id}',                     'App\Http\Controllers\HaseiController@descprint')->name('hasei.descprint');
Route::any('/hasei/AjaxInit',                           'App\Http\Controllers\HaseiController@AjaxInit')->name('hasei.AjaxInit');
Route::any('/hasei/AjaxGetHaseis',                      'App\Http\Controllers\HaseiController@AjaxGetHaseis')->name('hasei.AjaxGetHaseis');
Route::any('/hasei/AjaxGetHasei',                       'App\Http\Controllers\HaseiController@AjaxGetHasei')->name('hasei.AjaxGetHasei');
Route::any('/hasei/AjaxPutHasei',                       'App\Http\Controllers\HaseiController@AjaxPutHasei')->name('hasei.AjaxPutHasei');
Route::any('/hasei/AjaxPutFile/{id}',                   'App\Http\Controllers\HaseiController@AjaxPutFile');
Route::any('/hasei/AjaxDeleteFile/{id}/{name}',         'App\Http\Controllers\HaseiController@AjaxDeleteFile');

Route::get('/zido/create',                              'App\Http\Controllers\ZidoController@main')->name('zido.create');
Route::get('/zido/list',                                'App\Http\Controllers\ZidoController@main')->name('zido.list');
Route::get('/zido/list/{parent_kind}/{parent_id}',      'App\Http\Controllers\ZidoController@main')->name('zido.list2');
Route::get('/zido/edit/{id}',                           'App\Http\Controllers\ZidoController@main')->name('zido.edit');
Route::get('/zido/edit/{id}/{parent_kind}/{parent_id}/{hinmoku_id}/{hinmoku_suu}', 'App\Http\Controllers\ZidoController@main')->name('zido.edit2');
Route::get('/zido/descprint/{id}',                      'App\Http\Controllers\ZidoController@descprint')->name('zido.descprint');
Route::any('/zido/AjaxInit',                            'App\Http\Controllers\ZidoController@AjaxInit')->name('zido.AjaxInit');
Route::any('/zido/AjaxGetZidos',                        'App\Http\Controllers\ZidoController@AjaxGetZidos')->name('zido.AjaxGetZidos');
Route::any('/zido/AjaxGetZido',                         'App\Http\Controllers\ZidoController@AjaxGetZido')->name('zido.AjaxGetZido');
Route::any('/zido/AjaxPutZido',                         'App\Http\Controllers\ZidoController@AjaxPutZido')->name('zido.AjaxPutZido');
Route::any('/zido/AjaxPutFile/{shukko_id}/{id}',        'App\Http\Controllers\ZidoController@AjaxPutFile');
Route::any('/zido/AjaxDeleteFile/{shukko_id}/{id}',     'App\Http\Controllers\ZidoController@AjaxDeleteFile');

Route::get('/admin/zaiko/',                             'App\Http\Controllers\admin\ZaikoController@index');
Route::get('/admin/zaiko/index/{search_id?}',           'App\Http\Controllers\admin\ZaikoController@index')->name('zaiko.index');
Route::get('/admin/zaiko/create',                       'App\Http\Controllers\admin\ZaikoController@edit')->name('zaiko.create');
Route::get('/admin/zaiko/edit/{id}',                    'App\Http\Controllers\admin\ZaikoController@edit')->name('zaiko.edit');
Route::post('/admin/zaiko/store',                       'App\Http\Controllers\admin\ZaikoController@store')->name('zaiko.store');
Route::get('/admin/zaiko/rireki/{id?}',                 'App\Http\Controllers\admin\ZaikoController@rireki')->name('zaiko.rireki');
Route::get('/admin/zaiko/scaw/{id}',                    'App\Http\Controllers\admin\ZaikoController@scaw')->name('zaiko.scaw');
Route::post('/admin/zaiko/scawupdate',                  'App\Http\Controllers\admin\ZaikoController@scawupdate')->name('zaiko.scawupdate');
Route::any('/admin/zaiko/move/{id}',                    'App\Http\Controllers\admin\ZaikoController@move')->name('zaiko.move');
Route::any('/admin/zaiko/AjaxInit',                     'App\Http\Controllers\admin\ZaikoController@AjaxInit');
Route::any('/admin/zaiko/AjaxPutFile',                  'App\Http\Controllers\admin\ZaikoController@AjaxPutFile');
Route::any('/admin/zaiko/kcreate/{id}',                 'App\Http\Controllers\admin\ZaikoController@kcreate')->name('zaiko.kcreate');
Route::get('/admin/zaiko/indexredirect',function () {
    return redirect(route('zaiko.index', ['search_sort' => 16]));
})->name('zaiko.indexredirect');

Route::get('/admin/kzaiko/',                            'App\Http\Controllers\admin\KzaikoController@index');
Route::get('/admin/kzaiko/index',                       'App\Http\Controllers\admin\KzaikoController@index')->name('kzaiko.index');
Route::get('/admin/kzaiko/create',                      'App\Http\Controllers\admin\KzaikoController@edit')->name('kzaiko.create');
Route::get('/admin/kzaiko/edit/{id}',                   'App\Http\Controllers\admin\KzaikoController@edit')->name('kzaiko.edit');
Route::post('/admin/kzaiko/store',                      'App\Http\Controllers\admin\KzaikoController@store')->name('kzaiko.store');
Route::get('/admin/kzaiko/rireki/{id?}',                'App\Http\Controllers\admin\KzaikoController@rireki')->name('kzaiko.rireki');

Route::get('/admin/hinmoku/',                           'App\Http\Controllers\admin\HinmokuController@index');
Route::get('/admin/hinmoku/index',                      'App\Http\Controllers\admin\HinmokuController@index')->name('hinmoku.index');
Route::get('/admin/hinmoku/create',                     'App\Http\Controllers\admin\HinmokuController@edit')->name('hinmoku.create');
Route::get('/admin/hinmoku/edit/{id}',                  'App\Http\Controllers\admin\HinmokuController@edit')->name('hinmoku.edit');
Route::post('/admin/hinmoku/store',                     'App\Http\Controllers\admin\HinmokuController@store')->name('hinmoku.store');
Route::any('/admin/hinmoku/AjaxPutFile',                'App\Http\Controllers\admin\HinmokuController@AjaxPutFile');

Auth::routes();
