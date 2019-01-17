<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 9:29 PM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\CatalogCollection;
use App\Timeline\Domain\Collections\CatalogIdCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Catalog;
use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Exceptions\TimelineException;

class CatalogService
{
    /**
     * @var CatalogRepository
     */
    private $catalogRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * CatalogService constructor.
     * @param CatalogRepository $catalogRepository
     * @param UserRepository $userRepository
     */
    public function __construct(CatalogRepository $catalogRepository, UserRepository $userRepository)
    {
        $this->catalogRepository = $catalogRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return TypeaheadCollection
     * @throws TimelineException
     */
    public function getTypeahead(): TypeaheadCollection
    {
        try {
            return $this->catalogRepository->getTypeahead();
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveCatalogs();
        }
    }

    /**
     * @return CatalogCollection
     * @throws TimelineException
     */
    public function getAll(): CatalogCollection
    {
        try {
            return $this->catalogRepository->getAll();
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveCatalogs();
        }
    }

    /**
     * @param CatalogIdCollection $ids
     * @return CatalogCollection
     * @throws TimelineException
     */
    public function getByIds(CatalogIdCollection $ids): CatalogCollection
    {
        try {
            return $this->catalogRepository->getByIds($ids);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveCatalogs();
        }
    }

    /**
     * @param string $value
     * @return Catalog
     * @throws TimelineException
     */
    public function create(string $value): Catalog
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreateCatalog();
            }

            return $this->catalogRepository->create($value, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreateCatalog();
        }
    }

    /**
     * @param array $values
     * @return CatalogCollection
     * @throws TimelineException
     */
    public function bulkCreate(array $values): CatalogCollection
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreateCatalog();
            }

            return $this->catalogRepository->bulkCreate($values, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreateCatalog();
        }
    }

    /**
     * @param CatalogId $id
     * @param string $value
     * @return Catalog
     * @throws TimelineException
     */
    public function update(CatalogId $id, string $value): Catalog
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToUpdateCatalog($id);
            }

            return $this->catalogRepository->update($id, $value, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdateCatalog($id);
        }
    }

    /**
     * @param CatalogId $id
     * @return bool
     * @throws TimelineException
     */
    public function delete(CatalogId $id): bool
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToDeleteDateAttribute($id);
            }

            return $this->catalogRepository->delete($id);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToDeleteCatalog($id);
        }
    }
}