<?php
/**
 * @package NCMS
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 20.05.14
 */

namespace simpleCMS\core;

/**
 * Класс для формирования капчи
 */
class Captcha
{

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var array
     */
    protected $bg_color = ['r' => 255, 'g' => 255, 'b' => 255];

    /**
     * @var string
     */
    protected $color = ['r' => 0, 'g' => 0, 'b' => 0];

    /**
     * @var string
     */
    protected $form_id;

    /**
     * @var bool
     */
    protected $noise = true;

    /**
     * @param string $form_id
     */
    public function __construct($form_id)
    {
        $this->set_form_id($form_id);
    }

    public function initialize()
    {
        $this->set_code(substr(md5(uniqid("")), 0, 4));

        return $this;
    }

    /**
     * Генерирует изображение капчи в стандартный поток вывода
     * @return void
     * @throws \Exception
     */
    public function render()
    {
        $im = @imagecreate($this->get_width(), $this->get_height());
        if (!$im) throw new \Exception("Cannot initialize new GD image stream!");
        $bg = imagecolorallocate($im, $this->get_bg_color('r'), $this->get_bg_color('g'), $this->get_bg_color('b'));
        $char = $this->get_code();


        if ($this->get_noise()) {
//создаём шум на фоне
            $count = $this->get_width() / 5;
            for ($i = 0; $i <= $count; $i++) {
                $color = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255)); //задаём цвет
                imagesetpixel($im, rand(2, $this->get_width()), rand(2, $this->get_height()), $color); //рисуем пиксель
            }
        }

        $char = $this->get_code();
//выводим символы кода
        $dx = $this->get_width() / strlen($char);
        $dy = $this->get_height() - 20;

        for ($i = 0; $i < strlen($char); $i++) {
            $color = imagecolorallocate($im, $this->get_color('r'), $this->get_color('g'), $this->get_color('b')); //задаём цвет
            $x = 10 + $i * $dx;
            $y = rand(1, $dy);
            imagechar($im, 5, $x, $y, $char[$i], $color);
        }

        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

//создание рисунка в зависимости от доступного формата
        if (function_exists("imagepng")) {
            header("Content-type: image/png");
            imagepng($im);
        } elseif (function_exists("imagegif")) {
            header("Content-type: image/gif");
            imagegif($im);
        } elseif (function_exists("imagejpeg")) {
            header("Content-type: image/jpeg");
            imagejpeg($im);
        } else {
            die("No image support in this PHP server!");
        }
        imagedestroy($im);
    }

    /**
     * @param string $bg_color
     * @return $this
     */
    public function set_bg_color($bg_color)
    {
        try {
            $this->bg_color = $this->html_color_to_rgb($bg_color);
        } catch (\Exception $e) {
        }

        return $this;
    }

    /**
     * Возвращает массив rgb, или int цвет переданного канала фонового цвета
     * @param null|string $color r|g|b
     * @return array|int
     */
    public function get_bg_color($color = null)
    {
        if (!is_null($color) and array_key_exists($color, $this->bg_color)) return $this->bg_color[$color];

        return $this->bg_color;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function set_color($color)
    {
        try {
            $this->color = $this->html_color_to_rgb($color);
        } catch (\Exception $e) {
        }


        return $this;
    }

    /**
     * Возвращает массив rgb, или int цвет переданного канала цвета шрифта
     * @param null|string $color r|g|b
     * @return array|int
     */
    public function get_color($color = null)
    {
        if (!is_null($color) and array_key_exists($color, $this->color)) return $this->color[$color];

        return $this->color;
    }

    /**
     * @param string $form_id
     * @return $this
     */
    public function set_form_id($form_id)
    {
        $this->form_id = $form_id;

        return $this;
    }

    /**
     * @return string
     */
    public function get_form_id()
    {
        return $this->form_id;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function set_height($height)
    {
        $this->height = (int)$height;

        return $this;
    }

    /**
     * @return int
     */
    public function get_height()
    {
        if (empty($this->height)) return 40;

        return $this->height;
    }

    /**
     * @param int $width
     * @return $this
     */
    public function set_width($width)
    {
        $this->width = (int)$width;

        return $this;
    }

    /**
     * @return int
     */
    public function get_width()
    {
        if (empty($this->width)) return 140;

        return $this->width;
    }

    /**
     * @param boolean $noise
     * @return $this
     */
    public function set_noise($noise)
    {
        $this->noise = $noise;

        return $this;
    }

    /**
     * @return boolean
     */
    public function get_noise()
    {
        return (bool)$this->noise;
    }

    /**
     * @return string
     * @throws \Exception если капча не инициализирована
     */
    public function get_code()
    {
        $key = $this->get_captcha_key();
        if (!array_key_exists($key, $_SESSION) or empty($_SESSION[$key])) throw new \Exception('captcha is not initialized');

        return $_SESSION[$key];
    }


    /**
     * @param string $code
     * @return $this
     */
    protected function set_code($code)
    {
        $_SESSION[$this->get_captcha_key()] = $code;

        return $this;
    }

    /**
     * Конвертирует строку с html-цветом в rgb массив
     * @param string $html_color
     * @return array
     * @throws \Exception в случае неправильного формата цвета
     */
    protected function html_color_to_rgb($html_color)
    {
        $indexes = [0 => 'r', 2 => 'g', 4 => 'b'];
        $result = ['r' => 0, 'g' => 0, 'b' => 0];

        $html_color = str_replace('#', '', $html_color);
        if (($len = mb_strlen($html_color, 'utf-8')) != 6) throw new \Exception('color error');

        for ($i = 0; $i < $len; $i += 2) $result[$indexes[$i]] = hexdec(mb_substr($html_color, $i, 2));

        return $result;
    }

    /**
     * Возвращает ключ сессии хранения кода
     * @return string
     */
    protected function get_captcha_key()
    {
        return 'captcha_code_' . $this->get_form_id();
    }
}
