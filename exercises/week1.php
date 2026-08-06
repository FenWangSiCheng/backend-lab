<?php

declare(strict_types=1);

final class Student
{
    private const int PASSING_SCORE = 60;

    public function __construct(
        public readonly string $name,
        public readonly array $scores,
    ) {}

    public function average(): float
    {
        return array_sum($this->scores) / count($this->scores);
    }

    public function passed(): bool
    {
        return $this->average() >= self::PASSING_SCORE;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'scores' => $this->scores,
            'average' => round($this->average(), 1),
            'passed' => $this->passed(),
        ];
    }
}

$students = [
    new Student('张三', [80, 75, 90]),
    new Student('李四', [55, 62, 58]),
    new Student('王五', [95, 88, 92]),
];

$results = array_map(
    fn (Student $student): array => $student->toArray(),
    $students,
);

$outputFile = __DIR__.'/week1-result.json';
file_put_contents(
    $outputFile,
    json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL,
);

$savedResults = json_decode(file_get_contents($outputFile), true, flags: JSON_THROW_ON_ERROR);

foreach ($savedResults as $result) {
    $status = $result['passed'] ? '通过' : '未通过';
    echo "{$result['name']}：平均分 {$result['average']}，{$status}".PHP_EOL;
}
