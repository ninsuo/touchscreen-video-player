<?php

namespace DigiLoginBundle\Twig\Extension;

use BaseBundle\Base\BaseTwigExtension;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class DigiLoginExtension extends BaseTwigExtension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('mega_responsive', [$this, 'megaResponsive'], ['is_safe' => ['html']]),
        ];
    }

    public function megaResponsive($css, $view, $container)
    {
        $css = $this->getParameter('kernel.root_dir').'/../web/'.str_replace('../', '', $css);
        if (!is_file($css)) {
            throw new FileNotFoundException(null, 0, null, $css);
        }

        $view = $this->getParameter('kernel.root_dir').'/../src/'.str_replace('../', '', $view);
        if (!is_file($view)) {
            throw new FileNotFoundException(null, 0, null, $file);
        }

        $js = [];

        $maxFontSize = 8;
        $out         = array();
        $content     = str_replace(array("\n", "\r", "\t"), '', file_get_contents($css));
        preg_match_all("|\.([^\{]+)\{([^\}]+)\}|", $content, $out, PREG_PATTERN_ORDER);
        $count       = count($out[0]);
        for ($i = 0; ($i < $count); $i++) {
            list($name, $content) = array(trim($out[1][$i]), $out[2][$i]);
            $propsValues = explode(';', $content);
            foreach ($propsValues as $propValue) {
                if (empty($propValue)) {
                    continue;
                }
                $propValue      = explode(':', $propValue);
                $property       = trim($propValue[0]);
                $values         = explode(' ', trim($propValue[1]));
                $needProportion = false;
                $newValue       = null;
                foreach ($values as $value) {
                    if (!is_null($newValue)) {
                        $newValue .= ' ';
                    }
                    if (substr($value, -2) == 'px') {
                        $needProportion = true;
                        if ($property == 'font-size') {
                            $newValue .= "' + Math.max(ratio * ".intval($value).", {$maxFontSize}) + 'px";
                        } elseif ($property == 'border') {
                            $newValue .= "' + Math.max(ratio * ".intval($value).", 1) + 'px";
                        } else {
                            $newValue .= "' + ratio * ".intval($value)." + 'px";
                        }
                    } else {
                        $newValue .= $value;
                    }
                }
                if ($needProportion) {
                    $newValue = substr($newValue, strlen("' + "));
                    $js[] = "\$('.{$name}').css('{$property}', {$newValue}');";
                }
            }
        }

        $env = new \Twig_Environment(new \Twig_Loader_Filesystem([dirname($view)]));

        return $env->render(basename($view), [
            'js'        => $js,
            'container' => $container,
            'rand'      => $this->generateRandom(24),
        ]);
    }

    protected function generateRandom($size)
    {
        $letter = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
        $random = '';
        $length = strlen($letter);
        for ($i = 0; $i < $size; $i++) {
            $random .= $letter[rand() % $length];
        }

        return $random;
    }
}
