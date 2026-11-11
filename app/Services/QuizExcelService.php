<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizExcelService
{
    /**
     * Download Excel / CSV template pre-filled with sample UPSC questions
     */
    public function downloadTemplate(string $format = 'xlsx'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Quiz Questions');

        // Headers
        $headers = [
            'A1' => 'question_text',
            'B1' => 'option_a',
            'C1' => 'option_b',
            'D1' => 'option_c',
            'E1' => 'option_d',
            'F1' => 'correct_option',
            'G1' => 'explanation',
            'H1' => 'subject',
            'I1' => 'topic',
            'J1' => 'difficulty',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Style (Navy fill, bold white text)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0B1B3D'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // 3 High Quality Sample UPSC Prelims Questions
        $samples = [
            [
                'With reference to the Indian Constitution, consider the following statements regarding the Pardoning Power of the Governor:' . "\n" .
                '1. The Governor can pardon, reprieve, or commute a sentence of death.' . "\n" .
                '2. The Governor\'s pardoning power under Article 161 is subject to judicial review on grounds of arbitrariness.' . "\n" .
                'Which of the statements given above is/are correct?',
                '1 only',
                '2 only',
                'Both 1 and 2',
                'Neither 1 nor 2',
                'B',
                'Statement 1 is incorrect because only the President can pardon a sentence of death under Article 72. Statement 2 is correct because the Supreme Court in the Epuru Sudhakar case held that the pardoning power of both President and Governor is subject to judicial review.',
                'Polity & Governance',
                'Pardoning Powers',
                'medium',
            ],
            [
                'Which of the following is the primary objective of the PM Surya Ghar: Muft Bijli Yojana recently launched by the Government of India?',
                'Providing solar-powered agricultural pumps to all marginal farmers',
                'Installing rooftop solar systems to provide up to 300 units of free electricity monthly to one crore households',
                'Replacing all thermal power plants in northern India with nuclear reactors by 2030',
                'Supplying free lithium-ion batteries for electric two-wheelers in tier-2 cities',
                'B',
                'PM Surya Ghar: Muft Bijli Yojana aims to illuminate 1 crore households across India with up to 300 units of free solar-generated power every month while generating surplus income via grid net-metering.',
                'Economy & Energy',
                'Govt Schemes',
                'easy',
            ],
            [
                'In the context of international relations and global geopolitics, the term "Golden Crescent" is frequently mentioned in relation to:',
                'The advanced semiconductor manufacturing corridor spanning East Asia',
                'The historic maritime spice trade route crossing the Arabian Sea',
                'The principal illicit opium production and trafficking region encompassing Afghanistan, Iran, and Pakistan',
                'The space exploration partnership led by emerging economies',
                'C',
                'The Golden Crescent refers to South-Western Asia\'s illicit opium nexus comprising mountain valleys in Afghanistan, Iran, and Pakistan. Together with the Golden Triangle, it forms a major global security concern.',
                'Internal Security & IR',
                'Transnational Threats',
                'medium',
            ],
        ];

        $rowNum = 2;
        foreach ($samples as $row) {
            $colIndex = 1;
            foreach ($row as $val) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $rowNum, $val);
                $colIndex++;
            }
            $sheet->getRowDimension($rowNum)->setRowHeight(40);
            $rowNum++;
        }

        // Alignments & Wrap text
        $sheet->getStyle('A2:A4')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G2:G4')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F2:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J2:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(45);
        $sheet->getColumnDimension('H')->setWidth(22);
        $sheet->getColumnDimension('I')->setWidth(22);
        $sheet->getColumnDimension('J')->setWidth(15);

        // Freeze header
        $sheet->freezePane('A2');

        $fileName = 'quiz_questions_template.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import Questions from Excel/CSV file into a Quiz
     *
     * @return array{imported: int, errors: array<string>}
     */
    public function importQuestions(UploadedFile $file, Quiz $quiz): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if (in_array($ext, ['xlsx', 'xls'])) {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
        } else {
            // CSV parsing
            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        if (empty($rows) || count($rows) < 2) {
            return [
                'imported' => 0,
                'errors' => ['The uploaded sheet is empty or contains no data rows.'],
            ];
        }

        // Detect header column positions
        $headerRow = array_map(function ($col) {
            return strtolower(trim((string) $col));
        }, $rows[0]);

        $colMap = [
            'question_text' => $this->findColIndex($headerRow, ['question_text', 'question', 'stem', 'question text']),
            'option_a' => $this->findColIndex($headerRow, ['option_a', 'option a', 'opt_a', 'a']),
            'option_b' => $this->findColIndex($headerRow, ['option_b', 'option b', 'opt_b', 'b']),
            'option_c' => $this->findColIndex($headerRow, ['option_c', 'option c', 'opt_c', 'c']),
            'option_d' => $this->findColIndex($headerRow, ['option_d', 'option d', 'opt_d', 'd']),
            'correct_option' => $this->findColIndex($headerRow, ['correct_option', 'correct option', 'correct', 'answer', 'ans']),
            'explanation' => $this->findColIndex($headerRow, ['explanation', 'solution', 'exp']),
            'subject' => $this->findColIndex($headerRow, ['subject', 'subject_tag', 'dimension']),
            'topic' => $this->findColIndex($headerRow, ['topic', 'topic_tag']),
            'difficulty' => $this->findColIndex($headerRow, ['difficulty', 'level']),
        ];

        // Fallback default index positions if headers are standard order
        if ($colMap['question_text'] === false) $colMap['question_text'] = 0;
        if ($colMap['option_a'] === false) $colMap['option_a'] = 1;
        if ($colMap['option_b'] === false) $colMap['option_b'] = 2;
        if ($colMap['option_c'] === false) $colMap['option_c'] = 3;
        if ($colMap['option_d'] === false) $colMap['option_d'] = 4;
        if ($colMap['correct_option'] === false) $colMap['correct_option'] = 5;
        if ($colMap['explanation'] === false) $colMap['explanation'] = 6;
        if ($colMap['subject'] === false) $colMap['subject'] = 7;
        if ($colMap['topic'] === false) $colMap['topic'] = 8;
        if ($colMap['difficulty'] === false) $colMap['difficulty'] = 9;

        $importedCount = 0;
        $errors = [];
        $currentSortOrder = $quiz->questions()->count();

        // Process data rows starting from row 1 (0-indexed)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $rowNum = $i + 1;

            $stem = trim((string) ($row[$colMap['question_text']] ?? ''));
            if (empty($stem)) {
                // Skip completely blank rows
                continue;
            }

            $optA = trim((string) ($row[$colMap['option_a']] ?? ''));
            $optB = trim((string) ($row[$colMap['option_b']] ?? ''));
            $optC = trim((string) ($row[$colMap['option_c']] ?? ''));
            $optD = trim((string) ($row[$colMap['option_d']] ?? ''));

            if (empty($optA) || empty($optB)) {
                $errors[] = "Row {$rowNum}: Question has fewer than 2 required options. Skipped.";
                continue;
            }

            $optionsList = [];
            if (!empty($optA)) $optionsList[] = $optA;
            if (!empty($optB)) $optionsList[] = $optB;
            if (!empty($optC)) $optionsList[] = $optC;
            if (!empty($optD)) $optionsList[] = $optD;

            // Determine correct option index
            $rawCorrect = strtoupper(trim((string) ($row[$colMap['correct_option']] ?? 'A')));
            $correctIndex = 0;
            if (in_array($rawCorrect, ['A', '1'])) {
                $correctIndex = 0;
            } elseif (in_array($rawCorrect, ['B', '2'])) {
                $correctIndex = 1;
            } elseif (in_array($rawCorrect, ['C', '3'])) {
                $correctIndex = 2;
            } elseif (in_array($rawCorrect, ['D', '4'])) {
                $correctIndex = 3;
            }

            if ($correctIndex >= count($optionsList)) {
                $correctIndex = 0;
            }

            $explanation = trim((string) ($row[$colMap['explanation']] ?? ''));
            $subject = trim((string) ($row[$colMap['subject']] ?? 'General Studies'));
            $topic = trim((string) ($row[$colMap['topic']] ?? ''));
            $difficulty = strtolower(trim((string) ($row[$colMap['difficulty']] ?? 'medium')));
            if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
                $difficulty = 'medium';
            }

            // Create Question
            $question = Question::create([
                'language_id' => $quiz->language_id,
                'stem' => $stem,
                'explanation' => !empty($explanation) ? $explanation : null,
                'subject_tag' => !empty($subject) ? $subject : 'Current Affairs',
                'topic_tag' => !empty($topic) ? $topic : null,
                'difficulty' => $difficulty,
                'default_marks' => $quiz->marks_per_question,
                'default_negative_marks' => $quiz->negative_marks_per_question,
                'is_active' => true,
            ]);

            // Create Question Options
            foreach ($optionsList as $idx => $optText) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optText,
                    'is_correct' => ($idx === $correctIndex),
                    'sort_order' => $idx + 1,
                ]);
            }

            $currentSortOrder++;
            $quiz->questions()->attach($question->id, [
                'section_name' => !empty($subject) ? $subject : 'General Studies',
                'sort_order' => $currentSortOrder,
            ]);

            $importedCount++;
        }

        // Recalculate quiz total marks
        $quiz->update([
            'total_marks' => $quiz->questions()->count() * $quiz->marks_per_question,
        ]);

        return [
            'imported' => $importedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Find column index matching any synonym
     */
    protected function findColIndex(array $headers, array $synonyms): int|false
    {
        foreach ($headers as $idx => $val) {
            if (in_array($val, $synonyms)) {
                return $idx;
            }
        }
        return false;
    }
}
