import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { LoaderCircleIcon, WrenchIcon } from 'lucide-react';
import type { CellRenderProps } from '@forjedio/inertia-table-react';
import { useActionLoading } from './use-action-loading';

export function FixSslCell({ row }: CellRenderProps) {
  const hostedDomainId = row.id as number;
  const needsFix = Boolean(row.needs_ssl_fix);
  const { isLoading, start, finish } = useActionLoading();

  return (
    <Button
      type="button"
      variant="outline"
      size="sm"
      disabled={!needsFix || isLoading}
      aria-busy={isLoading}
      onClick={() => {
        if (!needsFix || !start()) {
          return;
        }

        router.post(
          route('kjjd84-vito-extended.primary-domains.fix-ssl', { hostedDomain: hostedDomainId }),
          {},
          {
            preserveScroll: true,
            onFinish: finish,
          },
        );
      }}
    >
      {isLoading ? <LoaderCircleIcon className="animate-spin" /> : <WrenchIcon />}
    </Button>
  );
}
