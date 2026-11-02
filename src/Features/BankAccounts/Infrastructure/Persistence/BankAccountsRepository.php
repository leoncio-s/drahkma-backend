<?php

namespace App\Features\BankAccounts\Infrastructure\Persistence;

use App\Features\BankAccounts\Application\Models\BankAccountsMapper;
use App\Features\BankAccounts\Domain\Entity\BankAccounts;
use App\Features\BankAccounts\Domain\Repository\BankAccountRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PDOException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BankAccountsRepository extends ServiceEntityRepository implements BankAccountRepositoryInterface
{

    public function __construct(ManagerRegistry $managerRegistry, private LoggerInterface $logger)
    {
        parent::__construct($managerRegistry, ORMBankAccountEntity::class);
    }

    public function save(BankAccounts $data) : ?BankAccounts
    {
        $account = null;
        $entityManager = $this->getEntityManager();

        $entityManager->beginTransaction();
        try {

            $select = $this->findOneBy(
                [
                    "userId" => $data->getUser(),
                    "agency" => $data->getAgency(),
                    "bankCode" => $data->getBankCode(),
                    "accountNumber" => $data->getAccountNumber()
                ]
            );

            if ($select !== null) {
                $account = BankAccountsMapper::fromORMToEntity($select);
            } else {
                $persitanceData = BankAccountsMapper::fromEntityToORM($data);
                $entityManager->persist($persitanceData);

                $entityManager->flush();
            
                $account = BankAccountsMapper::fromORMToEntity($persitanceData);

                $entityManager->commit();
            }
        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            $entityManager->rollback();
            throw $e;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            $entityManager->rollback();
            throw $e;
        }

        return $account;
    }

    public function update(BankAccounts $data) : ?BankAccounts
    {
        $entityManager = $this->getEntityManager();

        $entityManager->beginTransaction();
        try {
            $select = $this->findOneBy(
                [
                    "userId" => $data->getUser(),
                    "bankName" => $data->getBankName(),
                    "id" => $data->getId(),
                    "bankCode" => $data->getBankCode(),
                    "agency" => $data->getAgency(),
                    "accountNumber"  => $data->getAccountNumber(),
                ]
            );

            if ($select != null) 
            {
                throw new UnprocessableEntityHttpException("You already have a bank account with this data", code: 422);
            } else
            {

                $bankAccount = $entityManager->find(ORMBankAccountEntity::class, $data->getId());

                $bankAccount->setAccountNumber($data->getAccountNumber());
                $bankAccount->setAgency($data->getAgency());
                $bankAccount->setBankCode($data->getBankCode());
                $bankAccount->setBankName($data->getBankName());
                $bankAccount->setInitialBalance($data->getInitialBalance());

                $entityManager->flush();
                $account = BankAccountsMapper::fromORMToEntity($bankAccount);
            }
            $entityManager->commit();
            return $account;
        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            $entityManager->rollback();
            throw $e;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            $entityManager->rollback();
            throw $e;
        }
    }

    public function delete(BankAccounts $data): bool | array
    {

        $entityManager = $this->getEntityManager();

        $conn = $entityManager->getConnection();
        $sql = "SELECT count(id) as count FROM transfer_bank where bank_account=:id and user=:user";

        $it = $conn->executeQuery($sql, ['user' => $data->getUser(), "id" => $data->getId()]);
        if ($it->fetchOne() > 0)
        {
            throw new BadRequestException("Há itens vinculados a esta conta bancária, não é possível excluí-la.", 400);
        }

        $entityManager->beginTransaction();
        try {
            $prep = $this->findOneBy([
                "id" => $data->getId(),
                "userId" => $data->getUser()
            ]);

            if ($prep !== null) {
                $entityManager->remove($prep);
                $entityManager->flush();
                $entityManager->commit();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $entityManager->rollBack();
            $this->logger->error($e->getMessage(), $e->getTrace());
            throw $e;
        } catch (Exception $e) {
            $entityManager->rollBack();
            $this->logger->error($e->getMessage(), $e->getTrace());
            throw $e;
        }
    }

    public function get(int $id) : ?BankAccounts
    {
        try {
            $data = $this->find($id);
            if (count($data) > 0) {
                $account = BankAccountsMapper::fromORMToEntity($data);
                return $account;
            }
            return null;
        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            return null;
        }
    }

    public function getByUser(int $userId):array
    {
        try {
            $bankAccounts = [];
            $data = $this->findBy(['userId'=>$userId]);

            if (count($data) > 0) {
                foreach ($data as $ac) {
                    $account = BankAccountsMapper::fromORMToEntity($ac);
                    array_push($bankAccounts, $account);
                }
            }
            return $bankAccounts;
        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            throw $e;
        }
    }

    public function getByIdAndUser(int $id, int $userId):?BankAccounts
    {
        try {
            $data = $this->findOneBy(["userId" => $userId, "id" => $id]);

            if ($data != null) {
                return BankAccountsMapper::fromORMToEntity($data);
            }
            return null;
        } catch (PDOException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            return null;
        }
    }
}
