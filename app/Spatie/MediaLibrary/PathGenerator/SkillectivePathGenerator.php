<?php

namespace App\Spatie\MediaLibrary\PathGenerator;

use App\Models\User;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\PathGenerator\PathGenerator;
use Log;

class SkillectivePathGenerator implements PathGenerator
{
    public function getPath(Media $media) : string
    {
		if ($media->getAttribute('model_type')=='App\Models\User') {
			return 'users/' . $media->getAttribute('model_id') . '/' . $media->id.'/';
        }elseif ($media->getAttribute('model_type')=='App\Models\Testimonial') {
			return 'testimonials/' . $media->id . '/';
		}
        return 'media/' . $media->id . '/';
    }

    public function getPathForConversions(Media $media) : string
    {
        return $this->getPath($media).'c/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'/cri/';
    }
}