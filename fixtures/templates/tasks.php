<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$faker = \Faker\Factory::create();
$faker->addProvider(new \Faker\Provider\ru_RU\Person($faker));
$faker->addProvider(new \Faker\Provider\ru_RU\Address($faker));
$faker->addProvider(new \Faker\Provider\ru_RU\Company($faker));
$faker->addProvider(new \Faker\Provider\ru_RU\Text($faker));

$categoriesIds = [1,2,3,4,5,6,7,8];
$locationIds = [1,2,3,4,5,6,7,8];
$customerIds = [1,2,3];
$executorIds = [4,5,6];
$statusIds = [1,2];

return [
  'name' => $faker->sentence(5),
  'description' => $faker->paragraph(3),
  'category_id' => $categoriesIds[array_rand($categoriesIds)],
  'location_id' => $locationIds[array_rand($locationIds)],
  'budget' => $faker->numberBetween(500, 20000),
  'deadline' => $faker->dateTimeBetween('+1 week', '+1 months')->format('Y-m-d H:i:s'),
  'customer_id' => $customerIds[array_rand($customerIds)],
  'executor_id' => $faker->optional()->randomElement($executorIds),
  'status_id' => $statusIds[array_rand($statusIds)],
  'date' => $faker->dateTimeBetween('-1 days', 'now')->format('Y-m-d H:i:s'),
];
