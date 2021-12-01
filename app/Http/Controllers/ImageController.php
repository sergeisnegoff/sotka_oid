<?php

	namespace App\Http\Controllers;

	use Illuminate\Contracts\Filesystem\Filesystem;
	use League\Glide\Responses\LaravelResponseFactory;
	use League\Glide\ServerFactory;
	use League\Glide\Signatures\SignatureFactory;
	use League\Glide\Signatures\SignatureException;

	class ImageController extends Controller
	{

		public function show(Filesystem $filesystem, $path, $img)
		{
			$server = ServerFactory::create([
				'response' => new LaravelResponseFactory(app('request')),
				'source' => $filesystem->getDriver(),
				'cache' => $filesystem->getDriver(),
				'cache_path_prefix' => '.cache',
				'base_url' => '/img/'
			]);

			try {
				if (empty($img) || !file_exists($_SERVER['DOCUMENT_ROOT'].'/storage/'.str_replace('.', '/', $path).'/'.$img)) {
					return $server->getImageResponse('public/logo.png', array_merge(request()->all(), ['bg' => 'white', 'fit' => 'fit']));
				} else
					return $server->getImageResponse('public/'.str_replace('.', '/', $path).'/'.$img, request()->all());

			} catch (SignatureException $e) {
				return $server->getImageResponse('public/logo.png', array_merge(request()->all(), ['bg' => 'white', 'fit' => 'fit']));
			}
		}
	}
