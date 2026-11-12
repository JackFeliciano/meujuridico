<?php

declare(strict_types=1);

namespace MeuJuridico\Home;

final class Sample
{
    public static function office(): Office
    {
        $office = new Office();

        $office->addStatute(new Statute('ST-01', 'tenancy', 'Lei do Inquilinato', '22',
            'The landlord may not raise the rent during the term of the written lease without the tenant agreeing in writing.'));
        $office->addStatute(new Statute('ST-02', 'tenancy', 'Lei do Inquilinato', '23',
            'The tenant returns the deposit when the lease ends, less the cost of damage the tenant caused.'));
        $office->addStatute(new Statute('ST-03', 'labour', 'Consolidacao das Leis do Trabalho', '59',
            'Overtime carries a surcharge of fifty percent over the ordinary hourly rate.'));
        $office->addStatute(new Statute('ST-04', 'labour', 'Consolidacao das Leis do Trabalho', '74',
            'An employer pays the salary by the fifth working day of the month.'));
        $office->addStatute(new Statute('ST-05', 'consumer', 'Codigo de Defesa do Consumidor', '18',
            'A supplier answers for a defect it put on the market within thirty days of the complaint.'));
        $office->addStatute(new Statute('ST-06', 'consumer', 'Codigo de Defesa do Consumidor', '49',
            'A purchase made outside a shop may be cancelled within seven days of delivery.'));

        $office->addLawyer('dra.helena', ['tenancy', 'consumer']);
        $office->addLawyer('dr.marcos', ['labour']);
        $office->addLawyer('dra.bianca', ['tenancy', 'labour', 'consumer']);

        $office->open(new Matter('MJ-201', 'tenancy', 'Rita Alves', 'Deposit held after the lease ended'));
        $office->open(new Matter('MJ-202', 'labour', 'Joao Prado', 'Overtime unpaid for three months'));
        $office->open(new Matter('MJ-203', 'consumer', 'Carla Dias', 'Defective refrigerator, no repair in thirty days'));
        $office->open(new Matter('MJ-204', 'tenancy', 'Nuno Sa', 'Rent raised mid term without agreement'));
        $office->open(new Matter('MJ-205', 'consumer', 'Tiago Melo', 'Online purchase, cancellation refused'));

        $office->assign('MJ-201');
        $office->assign('MJ-202');
        $office->assign('MJ-203');
        $office->assign('MJ-204');

        $office->matter('MJ-202')->next();
        $office->matter('MJ-202')->next();
        $office->matter('MJ-205')->next();
        $office->matter('MJ-205')->next();
        $office->matter('MJ-205')->next();
        $office->matter('MJ-205')->next();
        $office->matter('MJ-205')->next();

        return $office;
    }

    public static function questions(): array
    {
        return [
            new Question('Can the landlord raise the rent during the lease?', 'tenancy', 'Rita Alves'),
            new Question('When does my deposit come back after the lease ends?', 'tenancy', 'Nuno Sa'),
            new Question('How much surcharge does overtime carry?', 'labour', 'Joao Prado'),
            new Question('I cancelled an online purchase, within how many days?', 'consumer', 'Tiago Melo'),
        ];
    }
}
