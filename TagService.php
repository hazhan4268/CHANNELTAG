<?php

namespace TelegramBot\Services;

use TelegramBot\Repositories\TagsRepo;

class TagService
{
    private $tagsRepo;
    
    public function __construct(TagsRepo $tagsRepo)
    {
        $this->tagsRepo = $tagsRepo;
    }
    
    /**
     * Get all tags
     */
    public function getAll()
    {
        return $this->tagsRepo->getAll();
    }
    
    /**
     * Get enabled tags/ids
     */
    public function getEnabled($type = null)
    {
        return $this->tagsRepo->getEnabledByType($type);
    }
    
    /**
     * Create tag/id
     */
    public function create($type, $value, $enabled = true, $sort = 0)
    {
        return $this->tagsRepo->create($type, $value, $enabled, $sort);
    }
    
    /**
     * Update tag/id
     */
    public function update($id, array $data)
    {
        return $this->tagsRepo->update($id, $data);
    }
    
    /**
     * Delete tag/id
     */
    public function delete($id)
    {
        return $this->tagsRepo->delete($id);
    }
    
    /**
     * Toggle enabled status
     */
    public function toggle($id)
    {
        $tag = $this->tagsRepo->find($id);
        if ($tag) {
            return $this->tagsRepo->update($id, ['enabled' => !$tag['enabled']]);
        }
        return false;
    }
}

