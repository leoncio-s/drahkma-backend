/*
    @author: Leoncio
    description: Samples inserts to add commons categories to general use
*/

SET FOREIGN_KEY_CHECKS=0;

insert ignore into categories(description, `user`) 
values 
    ('SALARIO', -1),
    ('ÁGUA', -1),
    ('LUZ', -1),
    ('TELEFONE', -1),
    ('INTERNET', -1),
    ('TRANSPORTE', -1),
    ('SAÚDE', -1),
    ('EDUCAÇÃO', -1),
    ('ASSINATURAS', -1);

SET FOREIGN_KEY_CHECKS=1;