<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\People;
use App\Helpers\NicHelper;

class PeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedPeople = [
            [
                'nic' => '199001010001',
                'title_id' => 'T01',
                'full_name' => 'Kamal Sahan Perera',
                'name_with_initials' => 'K.S. Perera',
                'gender_id' => 'G01',
                'date_of_birth' => '1990-01-10',
                'religion_id' => 'R01',
                'ethnicity_id' => 'E01',
                'civil_status_id' => 'C01',
                'health_condition' => '1',
                'blood_group_id' => 'B01',
                'email' => 'kamal.perera@example.com',
                'phone' => '0710000001',
                'district_id' => 'DIS001',
                'gn_division_id' => 'GND00001',
                'address_line1' => '101 Lake Road',
                'address_line2' => 'Colombo',
                'address_line3' => 'Western Province',
                'postal_code' => '00100',
            ],
            [
                'nic' => '199102020002',
                'title_id' => 'T02',
                'full_name' => 'Nadeesha Madurangi Silva',
                'name_with_initials' => 'N.M. Silva',
                'gender_id' => 'G02',
                'date_of_birth' => '1991-02-20',
                'religion_id' => 'R02',
                'ethnicity_id' => 'E02',
                'civil_status_id' => 'C02',
                'health_condition' => '1',
                'blood_group_id' => 'B02',
                'email' => 'nadeesha.silva@example.com',
                'phone' => '0710000002',
                'district_id' => 'DIS002',
                'gn_division_id' => 'GND00002',
                'address_line1' => '202 Temple Street',
                'address_line2' => 'Kandy',
                'address_line3' => 'Central Province',
                'postal_code' => '20000',
            ],
            [
                'nic' => '199203030003',
                'title_id' => 'T03',
                'full_name' => 'Ravindu Chanaka Fernando',
                'name_with_initials' => 'R.C. Fernando',
                'gender_id' => 'G01',
                'date_of_birth' => '1992-03-15',
                'religion_id' => 'R03',
                'ethnicity_id' => 'E03',
                'civil_status_id' => 'C01',
                'health_condition' => '0',
                'blood_group_id' => 'B03',
                'email' => 'ravindu.fernando@example.com',
                'phone' => '0710000003',
                'district_id' => 'DIS003',
                'gn_division_id' => 'GND00003',
                'address_line1' => '303 School Lane',
                'address_line2' => 'Galle',
                'address_line3' => 'Southern Province',
                'postal_code' => '80000',
            ],
            [
                'nic' => '199304040004',
                'title_id' => 'T04',
                'full_name' => 'Ishara Dilrukshi Jayasinghe',
                'name_with_initials' => 'I.D. Jayasinghe',
                'gender_id' => 'G02',
                'date_of_birth' => '1993-04-12',
                'religion_id' => 'R01',
                'ethnicity_id' => 'E01',
                'civil_status_id' => 'C03',
                'health_condition' => '1',
                'blood_group_id' => 'B04',
                'email' => 'ishara.jayasinghe@example.com',
                'phone' => '0710000004',
                'district_id' => 'DIS001',
                'gn_division_id' => 'GND00001',
                'address_line1' => '404 River View',
                'address_line2' => 'Matara',
                'address_line3' => 'Southern Province',
                'postal_code' => '81000',
            ],
            [
                'nic' => '199405050005',
                'title_id' => 'T05',
                'full_name' => 'Tharindu Nimesh Wijesinghe',
                'name_with_initials' => 'T.N. Wijesinghe',
                'gender_id' => 'G01',
                'date_of_birth' => '1994-05-08',
                'religion_id' => 'R02',
                'ethnicity_id' => 'E02',
                'civil_status_id' => 'C02',
                'health_condition' => '1',
                'blood_group_id' => 'B05',
                'email' => 'tharindu.wijesinghe@example.com',
                'phone' => '0710000005',
                'district_id' => 'DIS002',
                'gn_division_id' => 'GND00002',
                'address_line1' => '505 Green Park',
                'address_line2' => 'Kurunegala',
                'address_line3' => 'North Western Province',
                'postal_code' => '60000',
            ],
        ];

        foreach ($seedPeople as $person) {
            $nicPlain = NicHelper::normalize($person['nic']);

            People::updateOrCreate(
                ['nic_hash' => NicHelper::hash($nicPlain)],
                array_merge($person, [
                    'nic' => $nicPlain,
                    'nic_hash' => NicHelper::hash($nicPlain),
                    'profile_picture' => 'default.png',
                    'active_status' => '1',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}
