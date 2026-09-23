import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { LoaderCircleIcon, RocketIcon } from 'lucide-react';
import type { CellRenderProps } from '@forjedio/inertia-table-react';
import { useActionLoading } from './use-action-loading';

export function RedeployCell({ row }: CellRenderProps) {
  const serverId = row.server_id as number;
  const siteId = row.site_id as number;
  const canDeploy = Boolean(row.can_redeploy);
  const { isLoading, start, finish } = useActionLoading();

  return (
    <Button
      type="button"
      variant="outline"
      size="sm"
      disabled={!canDeploy || isLoading}
      aria-busy={isLoading}
      onClick={() => {
        if (!canDeploy || !start()) {
          return;
        }

        router.post(
          route('application.deploy', { server: serverId, site: siteId }),
          {},
          {
            preserveScroll: true,
            onFinish: finish,
          },
        );
      }}
    >
      {isLoading ? <LoaderCircleIcon className="animate-spin" /> : <RocketIcon />}
    </Button>
  );
}
