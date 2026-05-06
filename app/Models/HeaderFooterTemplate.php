<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class HeaderFooterTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type',
        'name',
        'content',
        'is_default',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'content' => 'json',
    ];

    /**
     * Get the user who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Filter by type (header or footer).
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Filter headers only.
     */
    public function scopeHeaders(Builder $query): Builder
    {
        return $query->ofType('header');
    }

    /**
     * Scope: Filter footers only.
     */
    public function scopeFooters(Builder $query): Builder
    {
        return $query->ofType('footer');
    }

    /**
     * Scope: Get the default template for a given type.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', 1);
    }

    /**
     * Scope: Get non-default templates.
     */
    public function scopeNonDefault(Builder $query): Builder
    {
        return $query->where('is_default', 0);
    }

    /**
     * Get the default header template.
     */
    public static function getDefaultHeader(): ?self
    {
        return static::headers()->default()->first();
    }

    /**
     * Get the default footer template.
     */
    public static function getDefaultFooter(): ?self
    {
        return static::footers()->default()->first();
    }

    /**
     * Set this template as the default for its type.
     * Automatically unsets other defaults for the same type.
     */
    public function setAsDefault(): void
    {
        // Clear other defaults of the same type
        static::ofType($this->type)->default()->update(['is_default' => 0]);

        // Set this as default
        $this->update(['is_default' => 1]);
    }

    /**
     * Check if this template is the default for its type.
     */
    public function isDefault(): bool
    {
        return (bool) $this->is_default;
    }

    /**
     * Get all headers (non-default and default).
     */
    public static function getAllHeaders()
    {
        return static::headers()->get();
    }

    /**
     * Get all footers (non-default and default).
     */
    public static function getAllFooters()
    {
        return static::footers()->get();
    }

    /**
     * Check if a given type string is valid.
     */
    public static function isValidType(string $type): bool
    {
        return in_array($type, ['header', 'footer']);
    }

    /**
     * Parse the JSON content and return it as an array.
     */
    public function getContentArray(): array
    {
        if (is_array($this->content)) {
            return $this->content;
        }

        return json_decode($this->content, true) ?? [];
    }

    /**
     * Update content from an array, automatically encoding to JSON.
     */
    public function setContentFromArray(array $content): self
    {
        $this->content = $content;
        return $this;
    }
}
