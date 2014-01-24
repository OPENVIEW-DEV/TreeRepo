<?php
namespace Openview\TreeRepoBundle\Document;
 use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
 
/**
 * @MongoDB\Document
 */
class StoredItem
{
    /** @MongoDB\Id */
    protected $id;
 
    /** @MongoDB\File */
    protected $file;
 
    /** @MongoDB\String */
    protected $filename;
 
    /** @MongoDB\String */
    protected $mimeType;
 
    /** @MongoDB\Date */
    protected $uploadDate;
 
    /** @MongoDB\Int */
    protected $length;
 
    /** @MongoDB\Int */
    protected $chunkSize;
 
    /** @MongoDB\String */
    protected $md5;
 
    public function getFile()
    {
        return $this->file;
    }
 
    public function setFile($file)
    {
        $this->file = $file;
    }
 
    public function getFilename()
    {
        return $this->filename;
    }
 
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }
 
    public function getMimeType()
    {
        return $this->mimeType;
    }
 
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }
 
    public function getChunkSize()
    {
        return $this->chunkSize;
    }
 
    public function getLength()
    {
        return $this->length;
    }
 
    public function getMd5()
    {
        return $this->md5;
    }
 
    public function getUploadDate()
    {
        return $this->uploadDate;
    }
}