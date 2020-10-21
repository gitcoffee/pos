<?php

namespace Webkul\Pos\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Storage;

/**
 * PosUser Repository
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosUserRepository extends Repository
{

    public function model() {
        return 'Webkul\Pos\Contracts\PosUser';
    }

    public function create(array $data)
    {
        
        $posUser = $this->model->create($data);
        
        if (isset($data['avatar']['image_1'])) {
            $image_path = $this->uploadImage($data, $posUser);
        }

        return $posUser;
    }

    public function getImageUrl($posUser)
    {
        // return Storage::url($posUser->image);
        return url('storage/' . $posUser->image);
    }

    public function update(array $data, $id)
    {
        $posUser = $this->find($id);
        
        if (isset($data['image'])) {
            unset($data['image']);
        }

        $posUser->update($data);

        if ( isset($data['avatar']) && is_array($data['avatar']) ) {
            $this->uploadImage($data, $posUser);
        }

        return $posUser;
    }

    /**
     * @param array $data
     * @param mixed $posUser
     * @return mixed
     */
    public function uploadImage($data, $posUser)
    {
        $image_path = '';

        if (isset($data['avatar'])) {
            foreach ($data['avatar'] as $imageId => $image) {
                $file = 'avatar.' . $imageId;
                $dir = 'pos_user/' . $posUser->id;

                if (str_contains($imageId, 'image_')) {
                    if (request()->hasFile($file)) {
                        if ($posUser->image) {
                            Storage::delete($posUser->image);
                        }

                        $image_path = request()->file($file)->store($dir);

                        $posUser->update([
                            'image' => $image_path
                        ]);
                    }
                }
            }
        }

        return $image_path;
    }

    public function delete($id)
    {
        $posUser = $this->find($id);

        if ($posUser->image) {
            Storage::delete($posUser->image);
        }
        
        $posUser->delete();
    }
}