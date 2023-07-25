<?php

namespace App\Entity;

use App\Repository\LogRepository;
use App\Trait\Timestamps;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?DateTime $input = null;

    #[ORM\Column]
    private ?DateTime $output = null;

    #[ORM\Column(length: 20)]
    private ?string $source = null;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInput(): ?DateTime
    {
        return $this->input;
    }

    public function setInput(DateTime $input): self
    {
        $this->input = $input;
        return $this;
    }

    public function getOutput(): ?DateTime
    {
        return $this->output;
    }

    public function setOutput(DateTime $output): self
    {
        $this->output = $output;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }
    
}
