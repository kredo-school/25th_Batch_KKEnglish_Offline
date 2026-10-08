<?php

namespace Database\Seeders;

use App\Models\LessonRecord;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class LessonRecordSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * ========================================
         * 記録を作成する予約
         *
         * ・status = completed
         * ・授業が終了している
         * ・Lesson Recordがまだない
         *
         * 昨日の授業も対象に含める
         * ========================================
         */
        $reservations = Reservation::query()
            ->with([
                'teacher.user',
                'material',
            ])
            ->whereHas('status', function ($query) {
                $query->where('status_code', 'completed');
            })
            ->where('end_at', '<=', now())
            ->whereDoesntHave('lessonRecord')
            ->orderBy('start_at')
            ->get();

        if ($reservations->isEmpty()) {
            $this->command?->info(
                'No completed reservations without lesson records were found.'
            );

            return;
        }

        /*
         * ========================================
         * SubjectとProgress Noteの組み合わせ
         * ========================================
         */
        $lessonData = [
            [
                'topic' => 'Speaking Practice',
                'notes' => [
                    'The student participated actively in the speaking activities and communicated ideas clearly.',
                    'The student showed good speaking confidence and responded well to follow-up questions.',
                    'The student was able to maintain a conversation with only occasional support from the teacher.',
                    'The student expressed opinions clearly and used appropriate vocabulary during the discussion.',
                    'The student is becoming more confident when speaking English in longer sentences.',
                    'The student actively participated in conversation practice and showed good communication skills.',
                ],
            ],
            [
                'topic' => 'Daily Conversation',
                'notes' => [
                    'The student practiced common expressions used in everyday conversations and responded naturally.',
                    'The student communicated well during the daily conversation exercises.',
                    'The student showed improvement in using natural expressions during casual conversation.',
                    'The student was able to answer everyday questions with increasing confidence.',
                    'The student practiced useful expressions for daily situations and used them successfully.',
                ],
            ],
            [
                'topic' => 'Grammar Review',
                'notes' => [
                    'The student showed good understanding of the grammar points introduced in the lesson.',
                    'The student understood the basic grammar structure but needs more practice using it in conversation.',
                    'The student was able to correct several grammar mistakes after receiving feedback.',
                    'The student demonstrated improvement in sentence structure and word order.',
                    'The student needs additional practice with verb tenses and sentence construction.',
                    'The student successfully completed the grammar exercises and understood the main rules.',
                ],
            ],
            [
                'topic' => 'Vocabulary Practice',
                'notes' => [
                    'The student learned several new vocabulary words and used them successfully in sentences.',
                    'The student demonstrated a good understanding of the vocabulary introduced during the lesson.',
                    'The student needs more practice recalling new vocabulary during spontaneous conversation.',
                    'The student was able to explain ideas using newly learned vocabulary.',
                    'The student showed improvement in choosing appropriate words for different situations.',
                    'The student learned useful expressions and was able to apply them during speaking practice.',
                ],
            ],
            [
                'topic' => 'Pronunciation Practice',
                'notes' => [
                    'The student practiced pronunciation carefully and showed improvement during repetition exercises.',
                    'The student needs more practice with word stress and natural English rhythm.',
                    'The student responded well to pronunciation corrections and was able to reproduce the target sounds.',
                    'The student showed improvement in pronunciation and speaking clarity.',
                    'The student should continue practicing difficult sounds and connected speech.',
                    'The student listened carefully to pronunciation corrections and successfully repeated the target words.',
                ],
            ],
            [
                'topic' => 'Listening Practice',
                'notes' => [
                    'The student understood most of the questions and responded appropriately.',
                    'The student demonstrated good listening comprehension throughout the lesson.',
                    'The student occasionally needed questions to be repeated but understood them after clarification.',
                    'The student showed improvement in understanding natural conversational English.',
                    'The student needs more practice listening to longer sentences at a natural speaking speed.',
                    'The student listened carefully and was able to identify the main points of the conversation.',
                ],
            ],
            [
                'topic' => 'Travel and Transportation',
                'notes' => [
                    'The student practiced useful English expressions for traveling and transportation.',
                    'The student successfully practiced asking for directions and transportation information.',
                    'The student learned useful vocabulary for airports, hotels, and public transportation.',
                    'The student participated well in travel-related role-play activities.',
                    'The student was able to communicate effectively in common travel situations.',
                ],
            ],
            [
                'topic' => 'Business Communication',
                'notes' => [
                    'The student practiced useful expressions for professional communication.',
                    'The student showed good understanding of common business English expressions.',
                    'The student practiced explaining ideas clearly in a professional setting.',
                    'The student participated actively in the business conversation exercises.',
                    'The student practiced useful expressions for meetings and workplace communication.',
                ],
            ],
            [
                'topic' => 'Asking Questions',
                'notes' => [
                    'The student practiced forming questions correctly and showed good improvement.',
                    'The student successfully used different question patterns during conversation practice.',
                    'The student needs more practice with word order when forming questions.',
                    'The student asked several relevant questions and participated actively in the lesson.',
                    'The student demonstrated improvement in asking follow-up questions naturally.',
                ],
            ],
            [
                'topic' => 'Giving Opinions',
                'notes' => [
                    'The student practiced expressing opinions clearly and supporting them with reasons.',
                    'The student communicated personal opinions confidently during the discussion.',
                    'The student learned useful expressions for agreeing and disagreeing politely.',
                    'The student participated actively in the discussion and explained ideas clearly.',
                    'The student showed improvement in expressing more detailed opinions in English.',
                ],
            ],
            [
                'topic' => 'Reading Comprehension',
                'notes' => [
                    'The student understood the main ideas of the reading material and answered the questions correctly.',
                    'The student demonstrated good reading comprehension during the lesson.',
                    'The student identified important information from the reading passage successfully.',
                    'The student needs more practice understanding unfamiliar vocabulary from context.',
                    'The student read the material carefully and explained the main points clearly.',
                ],
            ],
            [
                'topic' => 'Lesson Review',
                'notes' => [
                    'The student reviewed previous lesson content and demonstrated good understanding.',
                    'The student remembered most of the previously learned vocabulary and expressions.',
                    'The student successfully reviewed key grammar and vocabulary from previous lessons.',
                    'The student showed steady progress and responded well to review questions.',
                    'The student should continue reviewing previously learned expressions outside class.',
                ],
            ],
        ];

        /*
         * ========================================
         * Lesson Record作成
         * ========================================
         */
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($reservations as $reservation) {
            /*
             * completed_byにはUserの主キーを保存
             */
            $teacherUserId = $reservation->teacher?->user?->getKey();

            if ($teacherUserId === null) {
                $skippedCount++;

                $this->command?->warn(
                    "Reservation {$reservation->getKey()} skipped: teacher user was not found."
                );

                continue;
            }

            /*
             * 教材名と学習内容を選択
             */
            $materialName = $reservation->material?->name
                ?? 'English Lesson';

            $selectedLesson = fake()->randomElement($lessonData);

            $subject = $materialName
                . ' - '
                . $selectedLesson['topic'];

            $progressNote = fake()->randomElement(
                $selectedLesson['notes']
            );

            /*
             * 既存の記録は上書きしない
             */
            $lessonRecord = LessonRecord::firstOrCreate(
                [
                    'reservation_id' => $reservation->getKey(),
                ],
                [
                    'lesson_date' => $reservation
                        ->start_at
                        ->toDateString(),

                    'subject' => $subject,

                    'progress_note' => $progressNote,

                    'completed_by' => $teacherUserId,

                    'completed_at' => $reservation->end_at,
                ]
            );

            if ($lessonRecord->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        /*
         * ========================================
         * 実行結果
         * ========================================
         */
        $this->command?->info(
            "Lesson records created: {$createdCount}"
        );

        $this->command?->info(
            "Reservations skipped because teacher user was missing: {$skippedCount}"
        );
    }
}
