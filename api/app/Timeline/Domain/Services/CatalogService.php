<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 9:29 PM
 */

namespace App\Timeline\Domain\Services;


use App\Events\TimelineCatalogDeleted;
use App\Events\TimelineCatalogUpdated;
use App\Timeline\Domain\Collections\CatalogCollection;
use App\Timeline\Domain\Collections\CatalogIdCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Catalog;
use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Exceptions\TimelineException;

class CatalogService
{
    /**
     * @var CatalogRepository
     */
    private $catalogRepository;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * CatalogService constructor.
     * @param CatalogRepository $catalogRepository
     * @param UserService $userService
     */
    public function __construct(CatalogRepository $catalogRepository, UserService $userService)
    {
        $this->catalogRepository = $catalogRepository;
        $this->userService = $userService;
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
            throw TimelineException::ofUnableToRetrieveCatalogs($e);
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
            throw TimelineException::ofUnableToRetrieveCatalogs($e);
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
            $currentUser = $this->userService->getCurrentUser();

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
            throw TimelineException::ofUnableToCreateCatalog($e);
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
            $currentUser = $this->userService->getCurrentUser();

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
            throw TimelineException::ofUnableToCreateCatalog($e);
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
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToUpdateCatalog($id);
            }

            $catalog = $this->catalogRepository->update($id, $value, $currentUser->getId());

            TimelineCatalogUpdated::dispatch($id);

            return $catalog;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdateCatalog($id, $e);
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
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnableToDeleteCatalog($id);
            }

            $success =  $this->catalogRepository->delete($id);

            TimelineCatalogDeleted::dispatch();

            return $success;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToDeleteCatalog($id, $e);
        }
    }
}