<?php


namespace App\Data;

use App\Entity\Category;
use App\Entity\User;

class AssetData
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $placeOfDiscovery;

    /**
     * @var float
     */
    public $value;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPlaceOfDiscovery(): ?string
    {
        return $this->placeOfDiscovery;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }
}
