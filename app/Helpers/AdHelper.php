<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class AdHelper
{
    /**
     * Verifica se anúncios estão habilitados globalmente
     */
    public static function isEnabled(): bool
    {
        return Cache::remember('ads_enabled', 3600, function () {
            return (bool) SiteSetting::get('ads_enabled', true);
        });
    }

    /**
     * Verifica se o usuário atual deve ver anúncios
     * (apenas visitantes não logados)
     */
    public static function shouldShowAds(): bool
    {
        // Membros logados nunca veem anúncios
        if (auth()->check()) {
            return false;
        }

        return self::isEnabled();
    }

    /**
     * Obtém o código de um slot específico de anúncio
     */
    public static function getSlotCode(string $slotName): ?string
    {
        return Cache::remember("ad_slot_{$slotName}", 3600, function () use ($slotName) {
            return SiteSetting::get("ad_slot_{$slotName}");
        });
    }

    /**
     * Lista de slots disponíveis com descrições
     */
    public static function getAvailableSlots(): array
    {
        return [
            'header_banner' => 'Banner no topo da página (728x90)',
            'sidebar_top' => 'Topo da sidebar (300x250)',
            'sidebar_bottom' => 'Final da sidebar (300x250)',
            'between_posts' => 'Entre posts no feed (728x90)',
            'article_top' => 'Topo do artigo (728x90)',
            'article_bottom' => 'Final do artigo (728x90)',
            'popup_corner' => 'Popup no canto (300x250)',
            'interstitial' => 'Intersticial (tela cheia)',
        ];
    }

    /**
     * Limpa cache de anúncios (chamar ao atualizar configurações)
     */
    public static function clearCache(): void
    {
        Cache::forget('ads_enabled');
        
        foreach (array_keys(self::getAvailableSlots()) as $slot) {
            Cache::forget("ad_slot_{$slot}");
        }
    }
}
