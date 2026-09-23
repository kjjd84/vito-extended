import { NavItem } from '@/types';
import { GlobeIcon, PuzzleIcon, RocketIcon } from 'lucide-react';

export const pluginNavTitle = 'Vito Extended';

const pages: NavItem[] = [
  {
    title: 'Latest Deployments',
    href: '/vito-extended/latest-deployments',
    icon: RocketIcon,
  },
  {
    title: 'Primary Domains',
    href: '/vito-extended/primary-domains',
    icon: GlobeIcon,
  },
];

export function pluginNavItems(): NavItem[] {
  return pages;
}

export function vitoExtendedMainNavItems(): NavItem[] {
  return [
    {
      title: pluginNavTitle,
      href: pages[0].href,
      icon: PuzzleIcon,
      children: pages,
    },
  ];
}
