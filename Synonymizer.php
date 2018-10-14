<?php

/**
 * Class Synonymizer
 */
class Synonymizer {

    /**
     * Начало строки с вариациями
     *
     * @var string
     */
    private $startSymbol;

    /**
     * Окончание строки с вариациями
     *
     * @var string
     */
    private $endSymbol;

    /**
     * Разделитель
     *
     * @var string
     */
    private $delimert;

    /**
     * Текст для обработки
     *
     * @var string
     */
    private $text;

    /**
     * Сгенерированный текст
     *
     * @var string
     */
    private $outputText;


    /**
     * Synonimizer constructor.
     * @param string $start
     * @param string $end
     * @param string $delimert
     */
    public function __construct($start = '[', $end = ']', $delimert = '|') {

        // Символы, которые лучше экранировать



        $this->setStartSymbol($start);
        $this->setEndSymbol($end);


        $this->delimert = $delimert;
    }

    /**
     * Устанавливает значение начального символа в регулярном выражении
     *
     * @param $char
     */
    public function setStartSymbol($char) {
        $this->startSymbol = $char;
    }

    /**
     * Возвращает символ, по которому начинается поиск для регулярного выражения
     * Если необходимо экранировать символы - экранирует их
     *
     * @return string
     */
    private function getStartSymbolForRegexp() {
        return ( in_array($this->startSymbol, $this->getScreeningSymbols()) ) ? "\\$this->startSymbol" : $this->startSymbol;
    }

    /**
     * Устанавливает значение начального символа в регулярном выражении
     *
     * @param $char
     */
    public function setEndSymbol($char) {
        $this->endSymbol = $char;
    }

    /**
     * Возвращает символ, по которому начинается поиск для регулярного выражения
     * Если необходимо экранировать символы - экранирует их
     *
     * @return string
     */
    private function getEndSymbolForRegexp() {
        return ( in_array($this->endSymbol, $this->getScreeningSymbols()) ) ? "\\$this->endSymbol" : $this->endSymbol;
    }

    /**
     * Устанавливает значение текста, который будет обрабатываться скриптом
     *
     * @param $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * Получает результирующий (сгенерированный текст)
     *
     * @return string
     */
    public function getText() {
        return $this->synonimize();
    }

    /**
     * Логика для генерации текста
     *
     * @return string
     */
    private function synonimize() {

        // Выберем все вхождения вариантов по регулярному выражению

        // Все результаты будут в массиве $matches[0] (мы исспользуем 1 шаблон для поиска)
        preg_match_all($this->makeRegExp(), $this->text, $matches); 

        // Подготовим текст для замены, отформатировав строку до того формата, который необходим функции vsprintf
        $this->outputText = preg_replace($this->makeRegExp(), '%s', $this->text);

        // Генерируем текст для вывода
        $vspritnfParams = $this->getArrayWithValues($matches[0]);

        return vsprintf($this->outputText, $vspritnfParams);    
    }

    /**
     * Возвращает набор сгенерированных значений
     *
     * @param $matches
     * @return array
     * @throws Exception
     */
    private function getArrayWithValues($matches) {
        $data = array();

        if ( is_array($matches) ) {
            
            foreach ($matches as $match) {
                $variantsString = str_replace( [$this->startSymbol, $this->endSymbol], ['', ''], $match);
                
                $variantsArray = explode($this->delimert, $variantsString);
                $data[] = $variantsArray[array_rand($variantsArray)];
                
            }

        } else {
            throw new Exception('getRandomArray() require an array as it parametr');
        }

        
        return $data;
    }

    /**
     * Вернет шаблон регулярного выражения, которое хратит в себе варианты с словами
     *
     * @return string
     */
    private function makeRegExp() {
        $startSympolForRegexp = $this->getStartSymbolForRegexp();
        $endSympolForRegexp = $this->getEndSymbolForRegexp();
        $regexp = $startSympolForRegexp."[^\]]*".$endSympolForRegexp;
        return "/$regexp/";
    }

    /**
     * Символы, которые необходимо экранировать внутри регулярного выражения
     *
     * @return array
     */
    private function getScreeningSymbols() {
        return array('[', ']', '{', '}');
    }
}
