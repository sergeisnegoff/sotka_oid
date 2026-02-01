<?php

declare(strict_types=1);

namespace App\Orchid\Presenters;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Scout\Builder;
use Orchid\Screen\Contracts\Personable;
use Orchid\Screen\Contracts\Searchable;
use Orchid\Support\Presenter;

class UserPresenter extends Presenter implements Personable, Searchable
{
    /**
     * Returns the label for this presenter, which is used in the UI to identify it.
     */
    public function label(): string
    {
        return 'Users';
    }

    /**
     * Returns the title for this presenter, which is displayed in the UI as the main heading.
     */
    public function title(): string
    {
        return $this->entity->name;
    }

    /**
     * Returns the subtitle for this presenter, which provides additional context about the user.
     */
    public function subTitle(): string
    {
        // Voyager primary role
        $role = $this->entity->role?->name;

        // Если когда-то появится many-to-many roles, тоже поддержим
        $roles = null;
        if (method_exists($this->entity, 'roles')) {
            try {
                $roles = $this->entity->roles?->pluck('name')->implode(' / ');
            } catch (\Throwable $e) {
                $roles = null;
            }
        }

        $label = $roles ?: $role ?: '';

        return (string) Str::of($label)
            ->limit(20)
            ->whenEmpty(fn () => __('Regular User'));
    }

    /**
     * Returns the URL for this presenter, which is used to link to the user's edit page.
     */
    public function url(): string
    {
        return route('platform.systems.users.edit', $this->entity);
    }

    /**
     * Returns the URL for the user's Gravatar image, or a default image if one is not found.
     */
    public function image(): ?string
    {
        $avatar = $this->entity->avatar;
        if ($avatar) {
            if ($avatar == 'users/default.png') {
                return '/' . $avatar;
            } else {
                return Storage::url($avatar);
            }

        }
        $email = (string) ($this->entity->email ?? '');
        $email = trim(strtolower($email));

        // Если email пустой/NULL — вернём дефолтную картинку без gravatar-хеша
        $default = urlencode('https://raw.githubusercontent.com/orchidsoftware/.github/main/web/avatars/gravatar.png');

        if ($email === '') {
            return "https://www.gravatar.com/avatar/?d=$default";
        }

        $hash = md5($email);

        return "https://www.gravatar.com/avatar/$hash?d=$default";
    }

    /**
     * Returns the number of models to return for a compact search result.
     * This method is used by the search functionality to display a list of matching results.
     */
    public function perSearchShow(): int
    {
        return 3;
    }

    /**
     * Returns a Laravel Scout builder object that can be used to search for matching users.
     * This method is used by the search functionality to retrieve a list of matching results.
     */
    public function searchQuery(?string $query = null): Builder
    {
        return $this->entity->search($query);
    }
}
