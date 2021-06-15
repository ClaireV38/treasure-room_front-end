<?php


namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class AssetData
{
    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez saisir un titre.")
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

    /**
     * @var string
     * @Assert\Url(message="le lien doit être au format d'une url")
     */
    public $photo;

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

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $placeOfDiscovery
     */
    public function setPlaceOfDiscovery(string $placeOfDiscovery): void
    {
        $this->placeOfDiscovery = $placeOfDiscovery;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    /**
     * @param string $photo | null
     */
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }


}
