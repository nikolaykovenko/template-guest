-- phpMyAdmin SQL Dump
-- version 4.3.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 05 2015 г., 12:55
-- Версия сервера: 5.5.34
-- Версия PHP: 5.4.25

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `template-guest`
--

-- --------------------------------------------------------

--
-- Структура таблицы `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
`id` int(10) unsigned NOT NULL,
  `caption` varchar(255) NOT NULL DEFAULT '',
  `text` text
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `articles`
--

INSERT INTO `articles` (`id`, `caption`, `text`) VALUES
(1, 'Статья 1', '<p>Содержание статьи 1</p>'),
(2, 'Статья 2', '<p>Содержание статьи 2</p>');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
`id` int(10) unsigned NOT NULL,
  `article` int(10) unsigned NOT NULL,
  `parent_comment` int(10) unsigned DEFAULT NULL,
  `fio` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `article`, `parent_comment`, `fio`, `email`, `text`, `add_time`) VALUES
(2, 1, NULL, 'Пользователь', '', 'Содержание комментария', '2015-03-05 09:34:58'),
(16, 1, NULL, 'test', 'test@mail.ru', 'ee', '2015-03-05 10:14:53'),
(19, 2, NULL, 'Иванов', '', 'Комментарий ко второй статье', '2015-03-05 10:17:28'),
(20, 2, NULL, 'Федоров Федор', '', 'Еще один комментарий', '2015-03-05 10:17:50'),
(21, 2, 20, 'Тест', 'nikolay.kovenko@gmail.com', 'Ответ на коммент', '2015-03-05 10:18:09'),
(22, 2, 21, 'Николай', 'nikolay.kovenko@gmail.com', 'Ответ на ответ =)', '2015-03-05 10:23:49'),
(23, 2, 19, 'Иванов 2', '', 'Ответ на собственный коммент', '2015-03-05 10:39:04'),
(24, 2, 22, 'Тест', '', 'Ответ на ответ на ответ О_о', '2015-03-05 10:39:19'),
(25, 2, NULL, 'Ник', '', 'Просто коммент', '2015-03-05 10:39:31'),
(26, 2, NULL, '1', '2@fgf.ee', '333', '2015-03-05 10:53:14'),
(27, 2, NULL, 'test', 'nick@web2dev.com.ua', 'test', '2015-03-05 10:53:42');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `articles`
--
ALTER TABLE `articles`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
 ADD PRIMARY KEY (`id`), ADD KEY `article` (`article`), ADD KEY `parent_comment` (`parent_comment`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `articles`
--
ALTER TABLE `articles`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_comment`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
