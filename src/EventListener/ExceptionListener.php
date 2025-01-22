<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021-2025 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\EventListener;

use App\Badge\Provider\BadgeProviderFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Twig\Environment;

/**
 * ExceptionListener.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final readonly class ExceptionListener
{
    public function __construct(
        private BadgeProviderFactory $badgeProviderFactory,
        private Environment $twig,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $route = $event->getRequest()->get('_route');

        if ('app_homepage' === $route) {
            $response = $this->renderResponse();

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    private function renderResponse(): Response
    {
        return new Response(
            $this->twig->render('maintenance.html.twig', [
                'providers' => $this->badgeProviderFactory->getAll(),
            ]),
        );
    }
}
