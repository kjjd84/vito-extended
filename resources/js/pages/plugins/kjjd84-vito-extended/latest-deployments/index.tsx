import { Head, usePage } from '@inertiajs/react';
import Layout from '@/layouts/app/layout';
import Container from '@/components/container';
import HeaderContainer from '@/components/header-container';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { VitoTable } from '@/components/vito-table';
import { Deployment } from '@/types/deployment';
import type { CellRenderProps, InertiaTableData } from '@forjedio/inertia-table-react';
import { DomainCell } from '@/pages/plugins/kjjd84-vito-extended/components/domain-cell';
import { RedeployCell } from '@/pages/plugins/kjjd84-vito-extended/components/redeploy-cell';
import { pluginNavItems, pluginNavTitle } from '@/pages/plugins/kjjd84-vito-extended/components/nav-items';

const commitCell = ({ row }: CellRenderProps) => {
  const commit = (row.commit_data ?? {}) as Deployment['commit_data'];
  if (!commit.message) {
    return <span className="text-muted-foreground">No message</span>;
  }
  const href = commit.url && /^https?:\/\//.test(commit.url) ? commit.url : undefined;
  return href ? (
    <a href={href} target="_blank" rel="noopener noreferrer" className="text-primary inline-flex truncate font-mono">
      <span className="block max-w-[200px] overflow-x-hidden overflow-ellipsis">{commit.message}</span>
    </a>
  ) : (
    <span className="inline-flex truncate font-mono">
      <span className="block max-w-[200px] overflow-x-hidden overflow-ellipsis">{commit.message}</span>
    </span>
  );
};

const releaseCell = ({ row }: CellRenderProps) => (
  <div className="inline-flex items-center gap-2">
    {(row.release as string | null) ?? ''}
    {(row.active as boolean) && <Badge variant="default">active</Badge>}
  </div>
);

type Page = {
  deployments: InertiaTableData;
};

export default function LatestDeployments() {
  const page = usePage<Page>();

  return (
    <Layout secondNavTitle={pluginNavTitle} secondNavItems={pluginNavItems()}>
      <Head title="Latest Deployments" />

      <Container className="max-w-6xl">
        <HeaderContainer>
          <Heading
            title="Latest Deployments"
            description="Latest deployment for every site on every server in the current project."
          />
        </HeaderContainer>

        <VitoTable
          tableData={page.props.deployments}
          cellRenderers={{
            site_domain: DomainCell,
            commit: commitCell,
            release: releaseCell,
            deploy: (props: CellRenderProps) => <RedeployCell {...props} />,
          }}
        />
      </Container>
    </Layout>
  );
}
