<?php

namespace Database\Seeders;

use App\Enums\FormationStatus;
use App\Enums\LessonSourceType;
use App\Enums\QuestionType;
use App\Models\Formation;
use App\Models\FormationLesson;
use App\Models\Ministry;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::query()->where('email', 'admin@admin.com')->value('id');
        $ministries = Ministry::query()->pluck('id', 'slug');

        $formations = [
            [
                'title' => 'Fundamentos do Movimento Casa',
                'slug' => 'fundamentos-movimento-casa',
                'short_description' => 'Visão, missão e espiritualidade da comunidade.',
                'full_description' => 'Apresenta os pilares do Movimento Casa, cultura de serviço e compromisso missionário.',
                'ministry_slug' => 'formacao',
                'is_required' => true,
                'status' => FormationStatus::Published,
                'minimum_score' => 70,
                'certificate_enabled' => true,
                'workload_hours' => 8,
                'published_at' => now()->subMonths(3),
                'lessons' => [
                    ['title' => 'História e carisma', 'minutes' => 25],
                    ['title' => 'Espiritualidade comunitária', 'minutes' => 30],
                    ['title' => 'Compromissos do membro', 'minutes' => 20],
                ],
            ],
            [
                'title' => 'Formação de Liderança Pastoral',
                'slug' => 'formacao-lideranca-pastoral',
                'short_description' => 'Formação para serviço de coordenação e liderança.',
                'full_description' => 'Aborda liderança servidora, organização ministerial e gestão de equipes.',
                'ministry_slug' => 'acolhida',
                'is_required' => false,
                'status' => FormationStatus::Published,
                'minimum_score' => 75,
                'certificate_enabled' => true,
                'workload_hours' => 12,
                'published_at' => now()->subMonths(2),
                'lessons' => [
                    ['title' => 'Liderança servidora', 'minutes' => 35],
                    ['title' => 'Comunicação e feedback', 'minutes' => 30],
                    ['title' => 'Planejamento ministerial', 'minutes' => 40],
                ],
            ],
            [
                'title' => 'Boas práticas para ministérios',
                'slug' => 'boas-praticas-ministerios',
                'short_description' => 'Padronização de fluxos e condutas.',
                'full_description' => 'Material de referência para estruturação de rotinas dos ministérios.',
                'ministry_slug' => 'intercessao',
                'is_required' => false,
                'status' => FormationStatus::Draft,
                'minimum_score' => 70,
                'certificate_enabled' => false,
                'workload_hours' => 6,
                'published_at' => null,
                'lessons' => [
                    ['title' => 'Organização de escalas', 'minutes' => 20],
                    ['title' => 'Comunicação interna', 'minutes' => 20],
                ],
            ],
        ];

        foreach ($formations as $formationData) {
            $formation = Formation::query()->updateOrCreate(
                ['slug' => $formationData['slug']],
                [
                    'title' => $formationData['title'],
                    'short_description' => $formationData['short_description'],
                    'full_description' => $formationData['full_description'],
                    'ministry_id' => $ministries[$formationData['ministry_slug']] ?? null,
                    'is_required' => $formationData['is_required'],
                    'status' => $formationData['status'],
                    'minimum_score' => $formationData['minimum_score'],
                    'certificate_enabled' => $formationData['certificate_enabled'],
                    'workload_hours' => $formationData['workload_hours'],
                    'published_at' => $formationData['published_at'],
                    'created_by' => $creatorId,
                    'updated_by' => $creatorId,
                ],
            );

            foreach ($formationData['lessons'] as $index => $lessonData) {
                FormationLesson::query()->updateOrCreate(
                    [
                        'formation_id' => $formation->getKey(),
                        'display_order' => $index + 1,
                    ],
                    [
                        'title' => $lessonData['title'],
                        'description' => 'Conteúdo da aula: '.$lessonData['title'],
                        'support_text' => 'Material complementar disponível na área de suporte.',
                        'source_type' => LessonSourceType::Youtube,
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'video_path' => null,
                        'support_document_path' => null,
                        'support_document_paths' => null,
                        'estimated_duration_minutes' => $lessonData['minutes'],
                        'is_required' => true,
                        'is_active' => true,
                    ],
                );
            }

            $quiz = Quiz::query()->updateOrCreate(
                ['formation_id' => $formation->getKey()],
                [
                    'title' => 'Avaliação - '.$formation->title,
                    'minimum_score' => $formationData['minimum_score'],
                    'max_attempts' => 3,
                    'is_active' => $formationData['status'] === FormationStatus::Published,
                ],
            );

            $questions = [
                [
                    'statement' => 'Qual a principal finalidade desta formação?',
                    'question_type' => QuestionType::MultipleChoice,
                    'options' => [
                        ['label' => 'Aprofundar a vivência cristã no serviço', 'is_correct' => true],
                        ['label' => 'Somente cumprir uma obrigação administrativa', 'is_correct' => false],
                        ['label' => 'Substituir participação nos encontros', 'is_correct' => false],
                    ],
                ],
                [
                    'statement' => 'A formação contínua é importante para a missão?',
                    'question_type' => QuestionType::TrueFalse,
                    'options' => [
                        ['label' => 'Verdadeiro', 'is_correct' => true],
                        ['label' => 'Falso', 'is_correct' => false],
                    ],
                ],
            ];

            foreach ($questions as $questionIndex => $questionData) {
                $question = QuizQuestion::query()->updateOrCreate(
                    [
                        'quiz_id' => $quiz->getKey(),
                        'display_order' => $questionIndex + 1,
                    ],
                    [
                        'statement' => $questionData['statement'],
                        'question_type' => $questionData['question_type'],
                        'weight' => 1,
                        'is_active' => true,
                    ],
                );

                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    QuizOption::query()->updateOrCreate(
                        [
                            'quiz_question_id' => $question->getKey(),
                            'display_order' => $optionIndex + 1,
                        ],
                        [
                            'label' => $optionData['label'],
                            'is_correct' => $optionData['is_correct'],
                        ],
                    );
                }
            }
        }
    }
}
