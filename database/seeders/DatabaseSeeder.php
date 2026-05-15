<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(
            function () {
                // Insert John Doe into the users table
                DB::table('users')->insert([
                    'id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'username' => 'johndoe',
                    'email' => 'john.doe@example.com',
                    'password' => Hash::make('password'),
                    'created_at' => new \DateTime('2026-02-09 10:00:00'),
                    'updated_at' => new \DateTime('2026-02-09 10:00:00'),
                ]);

                // Insert Jane Doe into the users table
                DB::table('users')->insert([
                    'id' => 2,
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'username' => 'janedoe',
                    'email' => 'jane.doe@example.com',
                    'password' => Hash::make('password'),
                    'created_at' => new \DateTime('2026-02-09 11:00:00'),
                    'updated_at' => new \DateTime('2026-02-09 11:00:00'),
                ]);

                // Insert the poll test account used in the README.
                DB::table('users')->insert([
                    'id' => 5,
                    'first_name' => 'Test',
                    'last_name' => 'Test',
                    'username' => 'Test',
                    'email' => 'test@full.poll',
                    'password' => Hash::make('Pwd123456789'),
                    'created_at' => new \DateTime('2026-05-14 22:49:26'),
                    'updated_at' => new \DateTime('2026-05-14 22:49:26'),
                ]);

                // Insert some posts for John Doe
                DB::table('posts')->insert([
                    [
                        'id' => 1,
                        'user_id' => 1,
                        'title' => "John's First Post",
                        'content' => "This is the content of John's first post.",
                        'created_at' => new \DateTime('2026-02-09 12:00:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:00:00'),
                    ],
                    [
                        'id' => 2,
                        'user_id' => 1,
                        'title' => null,
                        'content' => "This is the content of John's second post.",
                        'created_at' => new \DateTime('2026-02-09 12:05:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:05:00'),
                    ],
                    [
                        'id' => 3,
                        'user_id' => 1,
                        'title' => null,
                        'content' => "This is the content of John's third post.",
                        'created_at' => new \DateTime('2026-02-09 12:10:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:10:00'),
                    ]
                ]);

                // Insert some posts for Jane Doe
                DB::table('posts')->insert([
                    [
                        'id' => 4,
                        'user_id' => 2,
                        'title' => null,
                        'content' => "This is the content of Jane's first post.",
                        'created_at' => new \DateTime('2026-02-09 12:05:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:05:00'),
                    ],
                    [
                        'id' => 5,
                        'user_id' => 2,
                        'title' => "Jane's Second Post",
                        'content' => "This is the content of Jane's second post.",
                        'created_at' => new \DateTime('2026-02-09 12:10:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:10:00'),
                    ],
                    [
                        'id' => 6,
                        'user_id' => 2,
                        'title' => "Jane's Third Post",
                        'content' => "This is the content of Jane's third post.",
                        'created_at' => new \DateTime('2026-02-09 12:15:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:15:00'),
                    ]
                ]);

                // Insert a post for the poll test account.
                DB::table('posts')->insert([
                    'id' => 7,
                    'user_id' => 5,
                    'title' => 'Compte de test Polls',
                    'content' => 'Ce compte contient des sondages de démonstration pour tester le dashboard.',
                    'created_at' => new \DateTime('2026-04-19 09:45:00'),
                    'updated_at' => new \DateTime('2026-04-19 09:45:00'),
                ]);

                // Insert some likes for John's posts
                DB::table('likes')->insert([
                    [
                        'user_id' => 2,
                        'post_id' => 1,
                        'reaction' => 'like',
                        'created_at' => new \DateTime('2026-02-09 12:20:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:20:00'),
                    ],
                    [
                        'user_id' => 1, // John likes his own post
                        'post_id' => 2,
                        'reaction' => 'love',
                        'created_at' => new \DateTime('2026-02-09 12:25:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:25:00'),
                    ],
                ]);

                // Insert some likes for Jane's posts
                DB::table('likes')->insert([
                    [
                        'user_id' => 1,
                        'post_id' => 4,
                        'reaction' => 'like',
                        'created_at' => new \DateTime('2026-02-09 12:30:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:30:00'),
                    ],
                    [
                        'user_id' => 1,
                        'post_id' => 5,
                        'reaction' => 'love',
                        'created_at' => new \DateTime('2026-02-09 12:35:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:35:00'),
                    ],
                    [
                        'user_id' => 2, // Jane likes her own post
                        'post_id' => 5,
                        'reaction' => 'wow',
                        'created_at' => new \DateTime('2026-02-09 12:40:00'),
                        'updated_at' => new \DateTime('2026-02-09 12:40:00'),
                    ],
                    [
                        'user_id' => 1,
                        'post_id' => 7,
                        'reaction' => 'like',
                        'created_at' => new \DateTime('2026-04-19 09:50:00'),
                        'updated_at' => new \DateTime('2026-04-19 09:50:00'),
                    ]
                ]);

                // Insert poll data for the README test account.
                DB::table('polls')->insert([
                    [
                        'id' => 23,
                        'user_id' => 5,
                        'title' => null,
                        'question' => 'Quel est le meilleur Star Wars ?',
                        'secret_token' => '0a24710b71ca59b19ad4730648d7bf71',
                        'color' => 'orange',
                        'is_draft' => false,
                        'allow_multiple_choices' => false,
                        'allow_vote_change' => true,
                        'results_public' => true,
                        'duration' => 12960000,
                        'started_at' => new \DateTime('2026-05-14 18:10:05'),
                        'ends_at' => new \DateTime('2026-10-11 18:10:05'),
                        'created_at' => new \DateTime('2026-05-14 18:10:05'),
                        'updated_at' => new \DateTime('2026-05-14 22:04:11'),
                    ],
                    [
                        'id' => 24,
                        'user_id' => 5,
                        'title' => null,
                        'question' => 'Quelle voiture choisis-tu ?',
                        'secret_token' => 'ead7e099547ecefb9011e980ae21bc15',
                        'color' => 'sky',
                        'is_draft' => false,
                        'allow_multiple_choices' => false,
                        'allow_vote_change' => false,
                        'results_public' => false,
                        'duration' => 6000,
                        'started_at' => new \DateTime('2026-05-14 18:36:38'),
                        'ends_at' => new \DateTime('2026-05-14 20:16:38'),
                        'created_at' => new \DateTime('2026-05-14 18:36:36'),
                        'updated_at' => new \DateTime('2026-05-14 18:39:05'),
                    ],
                    [
                        'id' => 25,
                        'user_id' => 5,
                        'title' => null,
                        'question' => 'Quel est votre sexe ?',
                        'secret_token' => '65a77fe4a81661a9996acc07e071a377',
                        'color' => 'violet',
                        'is_draft' => false,
                        'allow_multiple_choices' => false,
                        'allow_vote_change' => false,
                        'results_public' => false,
                        'duration' => 780,
                        'started_at' => new \DateTime('2026-05-14 18:42:07'),
                        'ends_at' => new \DateTime('2026-05-14 18:55:07'),
                        'created_at' => new \DateTime('2026-05-14 18:41:00'),
                        'updated_at' => new \DateTime('2026-05-14 18:42:07'),
                    ],
                    [
                        'id' => 26,
                        'user_id' => 5,
                        'title' => null,
                        'question' => 'Quelle est la capitale du Canada ?',
                        'secret_token' => '8da7891318c002f301f13682258a6fb5',
                        'color' => 'pink',
                        'is_draft' => false,
                        'allow_multiple_choices' => true,
                        'allow_vote_change' => false,
                        'results_public' => false,
                        'duration' => 1814400,
                        'started_at' => new \DateTime('2026-05-14 18:42:05'),
                        'ends_at' => new \DateTime('2026-06-04 18:42:05'),
                        'created_at' => new \DateTime('2026-05-14 18:41:53'),
                        'updated_at' => new \DateTime('2026-05-14 22:02:02'),
                    ],
                    [
                        'id' => 27,
                        'user_id' => 5,
                        'title' => null,
                        'question' => 'Quelle séries préférez-vous ?',
                        'secret_token' => 'e9e20c3e5a1f7dcb863684adbbc68737',
                        'color' => 'teal',
                        'is_draft' => false,
                        'allow_multiple_choices' => true,
                        'allow_vote_change' => true,
                        'results_public' => true,
                        'duration' => 7776000,
                        'started_at' => new \DateTime('2026-05-14 18:42:48'),
                        'ends_at' => new \DateTime('2026-08-12 18:42:48'),
                        'created_at' => new \DateTime('2026-05-14 18:42:48'),
                        'updated_at' => new \DateTime('2026-05-14 22:06:23'),
                    ],
                    [
                        'id' => 28,
                        'user_id' => 5,
                        'title' => null,
                        'question' => 'Laquelle est la plus chère ?',
                        'secret_token' => 'd9203e7c00a9323ae0b576b1db4e7cbd',
                        'color' => 'violet',
                        'is_draft' => true,
                        'allow_multiple_choices' => false,
                        'allow_vote_change' => true,
                        'results_public' => false,
                        'duration' => 36600,
                        'started_at' => new \DateTime('2026-05-14 21:27:23'),
                        'ends_at' => new \DateTime('2026-05-15 07:37:23'),
                        'created_at' => new \DateTime('2026-05-14 18:43:17'),
                        'updated_at' => new \DateTime('2026-05-14 22:05:18'),
                    ],
                    [
                        'id' => 29,
                        'user_id' => 5,
                        'title' => null,
                        'question' => 'Quel jeu(x) préférez-vous ?',
                        'secret_token' => '2327794f57ed136ae1363177727e33d9',
                        'color' => 'orange',
                        'is_draft' => false,
                        'allow_multiple_choices' => true,
                        'allow_vote_change' => true,
                        'results_public' => true,
                        'duration' => 60,
                        'started_at' => new \DateTime('2026-05-14 18:43:38'),
                        'ends_at' => new \DateTime('2026-05-14 18:44:38'),
                        'created_at' => new \DateTime('2026-05-14 18:43:38'),
                        'updated_at' => new \DateTime('2026-05-14 22:00:33'),
                    ],
                    [
                        'id' => 30,
                        'user_id' => 5,
                        'title' => null,
                        'question' => 'Qui est le plus connu ?',
                        'secret_token' => '5f19a11ba9e1d2771ffa54225b536743',
                        'color' => 'indigo',
                        'is_draft' => false,
                        'allow_multiple_choices' => false,
                        'allow_vote_change' => true,
                        'results_public' => false,
                        'duration' => 2592000,
                        'started_at' => new \DateTime('2026-05-14 20:16:45'),
                        'ends_at' => new \DateTime('2026-06-13 20:16:45'),
                        'created_at' => new \DateTime('2026-05-14 20:14:53'),
                        'updated_at' => new \DateTime('2026-05-14 21:49:50'),
                    ],
                ]);

                // Insert options for the test polls.
                DB::table('poll_options')->insert([
                    ['id' => 75, 'poll_id' => 23, 'label' => 'La revanche des sith', 'created_at' => new \DateTime('2026-05-14 18:10:05'), 'updated_at' => new \DateTime('2026-05-14 22:04:11')],
                    ['id' => 76, 'poll_id' => 23, 'label' => 'L\'empire contre-attaque', 'created_at' => new \DateTime('2026-05-14 18:10:05'), 'updated_at' => new \DateTime('2026-05-14 22:04:11')],
                    ['id' => 77, 'poll_id' => 23, 'label' => 'La menace fantôme', 'created_at' => new \DateTime('2026-05-14 22:04:11'), 'updated_at' => new \DateTime('2026-05-14 22:04:11')],
                    ['id' => 78, 'poll_id' => 23, 'label' => 'Un dernier espoir', 'created_at' => new \DateTime('2026-05-14 22:04:11'), 'updated_at' => new \DateTime('2026-05-14 22:04:11')],
                    ['id' => 79, 'poll_id' => 24, 'label' => 'Porsche', 'created_at' => new \DateTime('2026-05-14 18:36:36'), 'updated_at' => new \DateTime('2026-05-14 22:02:53')],
                    ['id' => 80, 'poll_id' => 24, 'label' => 'Bentley', 'created_at' => new \DateTime('2026-05-14 18:36:36'), 'updated_at' => new \DateTime('2026-05-14 22:02:53')],
                    ['id' => 81, 'poll_id' => 24, 'label' => 'BMW', 'created_at' => new \DateTime('2026-05-14 22:02:53'), 'updated_at' => new \DateTime('2026-05-14 22:02:53')],
                    ['id' => 82, 'poll_id' => 24, 'label' => 'Aston Martin', 'created_at' => new \DateTime('2026-05-14 22:02:53'), 'updated_at' => new \DateTime('2026-05-14 22:02:53')],
                    ['id' => 83, 'poll_id' => 24, 'label' => 'Ferrari', 'created_at' => new \DateTime('2026-05-14 22:02:53'), 'updated_at' => new \DateTime('2026-05-14 22:02:53')],
                    ['id' => 84, 'poll_id' => 25, 'label' => 'Homme', 'created_at' => new \DateTime('2026-05-14 18:41:00'), 'updated_at' => new \DateTime('2026-05-14 22:02:22')],
                    ['id' => 85, 'poll_id' => 25, 'label' => 'Femme', 'created_at' => new \DateTime('2026-05-14 18:41:00'), 'updated_at' => new \DateTime('2026-05-14 22:02:22')],
                    ['id' => 86, 'poll_id' => 25, 'label' => 'Autre', 'created_at' => new \DateTime('2026-05-14 22:02:22'), 'updated_at' => new \DateTime('2026-05-14 22:02:22')],
                    ['id' => 87, 'poll_id' => 26, 'label' => 'Ottawa', 'created_at' => new \DateTime('2026-05-14 18:41:53'), 'updated_at' => new \DateTime('2026-05-14 20:24:12')],
                    ['id' => 88, 'poll_id' => 26, 'label' => 'Vancouver', 'created_at' => new \DateTime('2026-05-14 18:41:53'), 'updated_at' => new \DateTime('2026-05-14 20:24:12')],
                    ['id' => 89, 'poll_id' => 26, 'label' => 'Montréal', 'created_at' => new \DateTime('2026-05-14 20:24:12'), 'updated_at' => new \DateTime('2026-05-14 22:06:04')],
                    ['id' => 90, 'poll_id' => 27, 'label' => 'Stranger Things', 'created_at' => new \DateTime('2026-05-14 18:42:48'), 'updated_at' => new \DateTime('2026-05-14 22:01:53')],
                    ['id' => 91, 'poll_id' => 27, 'label' => 'One Piece', 'created_at' => new \DateTime('2026-05-14 18:42:48'), 'updated_at' => new \DateTime('2026-05-14 22:01:53')],
                    ['id' => 92, 'poll_id' => 27, 'label' => 'Lost', 'created_at' => new \DateTime('2026-05-14 22:01:53'), 'updated_at' => new \DateTime('2026-05-14 22:01:53')],
                    ['id' => 93, 'poll_id' => 27, 'label' => 'The 100', 'created_at' => new \DateTime('2026-05-14 22:01:53'), 'updated_at' => new \DateTime('2026-05-14 22:01:53')],
                    ['id' => 94, 'poll_id' => 28, 'label' => 'Lamborghini Aventador SVJ', 'created_at' => new \DateTime('2026-05-14 18:43:17'), 'updated_at' => new \DateTime('2026-05-14 19:52:14')],
                    ['id' => 95, 'poll_id' => 28, 'label' => 'Rolls-Roys Cullinan', 'created_at' => new \DateTime('2026-05-14 18:43:17'), 'updated_at' => new \DateTime('2026-05-14 19:52:14')],
                    ['id' => 96, 'poll_id' => 28, 'label' => 'Porsche 992 GT3 RS', 'created_at' => new \DateTime('2026-05-14 19:52:14'), 'updated_at' => new \DateTime('2026-05-14 19:52:14')],
                    ['id' => 97, 'poll_id' => 28, 'label' => 'Aston Martin Vanquish', 'created_at' => new \DateTime('2026-05-14 19:54:54'), 'updated_at' => new \DateTime('2026-05-14 19:54:54')],
                    ['id' => 98, 'poll_id' => 29, 'label' => 'Call of Duty', 'created_at' => new \DateTime('2026-05-14 18:43:38'), 'updated_at' => new \DateTime('2026-05-14 22:00:33')],
                    ['id' => 99, 'poll_id' => 29, 'label' => 'League of Legends', 'created_at' => new \DateTime('2026-05-14 18:43:38'), 'updated_at' => new \DateTime('2026-05-14 22:00:33')],
                    ['id' => 100, 'poll_id' => 29, 'label' => 'Uncharted', 'created_at' => new \DateTime('2026-05-14 22:00:33'), 'updated_at' => new \DateTime('2026-05-14 22:00:33')],
                    ['id' => 101, 'poll_id' => 29, 'label' => 'Undertail', 'created_at' => new \DateTime('2026-05-14 22:00:33'), 'updated_at' => new \DateTime('2026-05-14 22:00:33')],
                    ['id' => 102, 'poll_id' => 29, 'label' => 'Minecraft', 'created_at' => new \DateTime('2026-05-14 22:00:33'), 'updated_at' => new \DateTime('2026-05-14 22:00:33')],
                    ['id' => 103, 'poll_id' => 30, 'label' => 'Michael Jackson', 'created_at' => new \DateTime('2026-05-14 20:14:53'), 'updated_at' => new \DateTime('2026-05-14 21:49:50')],
                    ['id' => 104, 'poll_id' => 30, 'label' => 'Michael Schumacher', 'created_at' => new \DateTime('2026-05-14 20:14:53'), 'updated_at' => new \DateTime('2026-05-14 21:49:50')],
                    ['id' => 105, 'poll_id' => 30, 'label' => 'George Michael', 'created_at' => new \DateTime('2026-05-14 20:14:53'), 'updated_at' => new \DateTime('2026-05-14 21:49:50')],
                ]);

                // Insert votes so the test account has dashboard data with results.
                DB::table('poll_votes')->insert([
                    ['id' => 10, 'poll_id' => 23, 'user_id' => 5, 'poll_option_id' => 75, 'created_at' => new \DateTime('2026-05-14 22:08:52'), 'updated_at' => new \DateTime('2026-05-14 22:08:52')],
                    ['id' => 11, 'poll_id' => 26, 'user_id' => 5, 'poll_option_id' => 88, 'created_at' => new \DateTime('2026-05-14 20:29:02'), 'updated_at' => new \DateTime('2026-05-14 20:29:02')],
                    ['id' => 12, 'poll_id' => 26, 'user_id' => 5, 'poll_option_id' => 87, 'created_at' => new \DateTime('2026-05-14 20:29:02'), 'updated_at' => new \DateTime('2026-05-14 20:29:02')],
                    ['id' => 13, 'poll_id' => 30, 'user_id' => 5, 'poll_option_id' => 103, 'created_at' => new \DateTime('2026-05-14 20:52:42'), 'updated_at' => new \DateTime('2026-05-14 20:52:42')],
                ]);
            }
        );
    }
}
