<?php

/*
 * This file is part of the Sami utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sami;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;


class Parser
{
    protected $shortDesc;
    protected $longDesc;
    protected $tags = array();
    protected $errors = array();

    public function addTag($key, $value)
    {
        $this->tags[$key][] = $value;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getOtherTags()
    {
        $tags = $this->tags;
        unset($tags['param'], $tags['return'], $tags['var'], $tags['throws']);

        foreach ($tags as $name => $values) {
            foreach ($values as $i => $value) {
                $tags[$name][$i] = is_string($value) ? explode(' ', $value) : $value;
            }
        }

        return $tags;
    }

    public function getTag($key)
    {
        return isset($this->tags[$key]) ? $this->tags[$key] : array();
    }

    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    public function getLongDesc()
    {
        return $this->longDesc;
    }

    public function setShortDesc($shortDesc)
    {
        $this->shortDesc = $shortDesc;
    }

    public function setLongDesc($longDesc)
    {
        $this->longDesc = $longDesc;
    }

    public function getDesc()
    {
        return $this->shortDesc."\n\n".$this->longDesc;
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}



class DocBlockParser
{
    public function parse($comment, ParserContext $context)
    {
        $docBlock = null;
        $errorMessage = '';

        try {
            $docBlockContext = new DocBlock\Context($context->getNamespace(), $context->getAliases() ?: array());
            $docBlock = new DocBlock((string) $comment, $docBlockContext);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $result = new DocBlockNode();

        if ($errorMessage) {
            $result->addError($errorMessage);

            return $result;
        }

        $result->setShortDesc($docBlock->getShortDescription());
        $result->setLongDesc((string) $docBlock->getLongDescription());

        foreach ($docBlock->getTags() as $tag) {
            $result->addTag($tag->getName(), $this->parseTag($tag));
        }

        return $result;
    }

    public function getTag($string)
    {
        return Tag::createInstance($string);
    }

    protected function parseTag(DocBlock\Tag $tag)
    {
        switch (substr(get_class($tag), 38)) {
            case 'VarTag':
            case 'ReturnTag':
                return array(
                    $this->parseHint($tag->getTypes()),
                    $tag->getDescription(),
                );
            case 'PropertyTag':
            case 'PropertyReadTag':
            case 'PropertyWriteTag':
            case 'ParamTag':
                return array(
                    $this->parseHint($tag->getTypes()),
                    ltrim($tag->getVariableName(), '$'),
                    $tag->getDescription(),
                );
            case 'ThrowsTag':
                return array(
                    $tag->getType(),
                    $tag->getDescription(),
                );
            default:
                return $tag->getContent();
        }
    }

    protected function parseHint($rawHints)
    {
        $hints = array();
        foreach ($rawHints as $hint) {
            if ('[]' == substr($hint, -2)) {
                $hints[] = array(substr($hint, 0, -2), true);
            } else {
                $hints[] = array($hint, false);
            }
        }

        return $hints;
    }
}
