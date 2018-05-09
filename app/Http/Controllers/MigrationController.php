<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Album;
use App\Audio;
use App\AudioNew;
use App\AudioUAT;
use App\AlbumUAT;
use App\ArtistUAT;
use App\AlbumNew;
use App\Artist;
use App\ArtistNew;
use App\Country;
use App\GenreNew;
use App\TagNew;
use App\AudioTag;
use Carbon\Carbon;

class MigrationController extends BaseController
{
	public function migrateAlbum()
	{
		Album::chunk(100, function ($albums) {
		  foreach ($albums as $album) {
		  	$storeDate = $album->year . '-00-00';
		  	$saveAlbum = new AlbumNew();
		  	$saveAlbum->id = $album->id;
		  	$saveAlbum->name = $album->album_title;
		  	if (!empty($album->album_thumbnail) && $album->album_thumbnail !== 'defaults/default.jpg') {
		  		$saveAlbum->image = $album->album_thumbnail;
		  	}
		  	$saveAlbum->slug = $album->album_slug;
		  	$saveAlbum->artist_id = $album->artist_id;
		  	$saveAlbum->is_active = $album->published;
		  	if ($album->year !== 'Unknown' && $album->year !== 0) {
		  		$saveAlbum->release_date = Carbon::parse($storeDate)->format('Y-m-d');
		  	}
		  	$saveAlbum->save();

		  }
		});
		return 'success';	
	}

	public function migrateArtist()
	{
		Artist::chunk(100, function ($artists) {
		  foreach ($artists as $artist) {

		  	$countryId = Country::where('iso', $artist->artist_country)->first();

		  	$saveArtist = new ArtistNew();
		  	$saveArtist->id = $artist->id;
		  	$saveArtist->name = $artist->artist_name;
		  	if (!empty($artist->artist_thumbnail) && $artist->artist_thumbnail !== 'defaults/default.jpg') {
		  		$saveArtist->image = $artist->artist_thumbnail;
		  	}
		  	$saveArtist->biography = $artist->biography;
		  	if (!empty($countryId)) {
		  		$saveArtist->country_id = $countryId->id;
		  	}
		  	$saveArtist->is_active = $artist->public;
		  	$saveArtist->slug = $artist->slug;
		  	$saveArtist->save();

		  }
		});
		return 'success';		  

	}

	public function migrateSongs()
	{
		Audio::chunk(100, function ($audios) {
		  foreach ($audios as $audio) {

		  	$genre = GenreNew::where('name', $audio->song_genre)->first();
		  	$album = AlbumNew::find($audio->album_id);

		  	$saveAudio = new AudioNew();
		  	$saveAudio->name = $audio->song_title;
		  	$saveAudio->duration = $audio->length;
		  	if (!empty($album)) {
		  		$saveAudio->image = $album->image;
			  	$saveAudio->artist_id = $album->artist_id;
		  	}
		  	$saveAudio->album_id = $audio->album_id;
		  	$saveAudio->source_url = $audio->song_url;
		  	if (!empty($genre)) {
		  		$saveAudio->genre_id = $genre->id;
		  	}
		  	$saveAudio->is_active = 0;
		  	$saveAudio->slug = $audio->song_slug;
		  	$saveAudio->save();

		  	if (!empty($audio->tags)) {
			  	if (strpos($audio->tags, ';') !== false) {
					$splitArray = explode(';', $audio->tags);
					foreach ($splitArray as $value) {
						$tagText = str_replace('.','',trim($value));
						$tagContent = TagNew::where('name', $tagText)->first();
						if (!empty($tagContent)) {
							$audioTags = new AudioTag();
							$audioTags->audio_id = $saveAudio->id;
							$audioTags->tag_id = $tagContent->id;
							$audioTags->save();
						}
					}
				}
				if (strpos($audio->tags, '/') !== false) {
					$splitArray = explode('/', $audio->tags);
					foreach ($splitArray as $value) {
						$tagText = str_replace('.','',trim($value));
						$tagContent = TagNew::where('name', $tagText)->first();
						if (!empty($tagContent)) {
							$audioTags = new AudioTag();
							$audioTags->audio_id = $saveAudio->id;
							$audioTags->tag_id = $tagContent->id;
							$audioTags->save();
						}
					}
				}
				if (strpos($audio->tags, ',') !== false) {
					$splitArray = explode(',', $audio->tags);
					foreach ($splitArray as $value) {
						$tagText = str_replace('.','',trim($value));
						$tagContent = TagNew::where('name', $tagText)->first();
						if (!empty($tagContent)) {
							$audioTags = new AudioTag();
							$audioTags->audio_id = $saveAudio->id;
							$audioTags->tag_id = $tagContent->id;
							$audioTags->save();
						}
					}
				}
		  	}
		  }
		});
		return 'success';
	}

	public function migrateGenreFromAudios()
	{
		$genres = Audio::select('song_genre')->whereNotNull('song_genre')->where('song_genre', '!=', '')->groupBy('song_genre')->get()->pluck('song_genre')->toArray();

		$finalArray = [];
		foreach ($genres as $value) {
			$newGenre = explode(',', $value);
			foreach ($newGenre as $genre) {
				$val = trim($genre);
			    if(!in_array($val, $finalArray, true)){
			        array_push($finalArray, $val);
			    }
			}

		}

		foreach ($finalArray as $genre) {
			$saveGenre = new GenreNew;
			$saveGenre->name = $genre;
			$saveGenre->slug = str_slug($genre);
			$saveGenre->is_active = 1;
			$saveGenre->save();
		}
		return 'success';
	}

	public function migrateTagsFromAudios()
	{
		$tags = Audio::select('tags')->whereNotNull('tags')->where('tags', '!=', '')->groupBy('tags')->where('tags', '!=', 'Unknown')->get()->pluck('tags')->toArray();		
		$finalArray = [];
		foreach ($tags as $tag) {
			if (strpos($tag, ';') !== false) {
				$splitArray = explode(';', $tag);
				foreach ($splitArray as $value) {
					$tagText = str_replace('.','',trim($value));
				    if(!in_array($tagText, $finalArray, true)){	
				    	if ($tagText) {
				        	array_push($finalArray, $tagText);
				    	}
				    }
				}
			}
			if (strpos($tag, '/') !== false) {
				$splitArray = explode('/', $tag);
				foreach ($splitArray as $value) {
					$tagText = str_replace('.','',trim($value));
				    if(!in_array($tagText, $finalArray, true)){	
				    	if ($tagText) {
				        	array_push($finalArray, $tagText);
				    	}
				    }
				}
			}
			if (strpos($tag, ',') !== false) {
				$splitArray = explode(',', $tag);
				foreach ($splitArray as $value) {
					$tagText = str_replace('.','',trim($value));
				    if(!in_array($tagText, $finalArray, true)){
				    	if ($tagText) {
				        	array_push($finalArray, $tagText);
				    	}
				    }
				}
			}			
		}
		foreach ($finalArray as $tag) {
			$saveTag = new TagNew;
			$saveTag->name = $tag;
			$saveTag->save();
		}
		return 'success';
	}

	public function fillUat()
	{
		// $newAudio = AudioNew::inRandomOrder()->whereNotNull('audio_url')->get()->toArray();
		// AudioUAT::insert($newAudio);

		// $allAudios = AudioUAT::offset(800)->limit(400)->get();
		// $allAudios = AudioUAT::get();
		// foreach ($allAudios as $value) {
			// if (!ArtistUAT::where('id', $value->artist_id)->exists()) {
			// 	$artist = ArtistNew::find($value->artist_id)->toArray();
			// 	ArtistUAT::insert($artist);
			// }
			// if (!AlbumUAT::where('id', $value->album_id)->exists()) {
			// 	$album = AlbumNew::find($value->album_id)->toArray();
			// 	AlbumUAT::insert($album);
			// }

			// if (empty($value->recently_listened_count)) {
			// 	$value->recently_listened_count = mt_rand (1,300);
			// 	$value->update();
			// }
				// $value->explore_listened_count = mt_rand (1,200);
				// $value->update();

				// $value->favourites_count = mt_rand (1,200);
				// $value->update();
		// }

		return 'save';
	}
}