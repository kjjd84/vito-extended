import { Head, usePage } from '@inertiajs/react';
import Layout from '@/layouts/app/layout';
import Container from '@/components/container';
import HeaderContainer from '@/components/header-container';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { VitoTable } from '@/components/vito-table';
import { HostedDomain } from '@/types/hosted-domain';
import type { CellRenderProps, InertiaTableData } from '@forjedio/inertia-table-react';
import { DomainCell } from '@/pages/plugins/kjjd84-vito-extended/components/domain-cell';
import { FixSslCell } from '@/pages/plugins/kjjd84-vito-extended/components/fix-ssl-cell';
import { pluginNavItems, pluginNavTitle } from '@/pages/plugins/kjjd84-vito-extended/components/nav-items';

function CertificateCell({ row }: CellRenderProps) {
  const hostedDomain = row as HostedDomain & {
    site_webserver?: string;
    site_webserver_creates_site_ssls?: boolean;
  };
  const ssl = hostedDomain.ssl as { id: number; type: string; domains: string[]; expires_at: string } | null;
  const { ssl_method } = hostedDomain;
  const createsSiteSSLs = hostedDomain.site_webserver_creates_site_ssls ?? false;
  const webserver = hostedDomain.site_webserver ?? 'webserver';
  const webserverName = webserver.charAt(0).toUpperCase() + webserver.slice(1);

  if (ssl_method === 'letsencrypt' && !createsSiteSSLs) {
    return <Badge variant="outline">{webserverName} Managed SSL</Badge>;
  }

  if (ssl_method === 'letsencrypt' && createsSiteSSLs && ssl?.id) {
    return (
      <TooltipProvider>
        <Tooltip>
          <TooltipTrigger asChild>
            <Badge variant="outline" className="cursor-default">
              Site Certificate
            </Badge>
          </TooltipTrigger>
          <TooltipContent>ID: {ssl.id}</TooltipContent>
        </Tooltip>
      </TooltipProvider>
    );
  }

  if (!ssl) {
    return <span>-</span>;
  }

  const sslDomains = ssl.domains ?? [];

  return (
    <div className="flex flex-wrap gap-1">
      <Badge variant="info">{(ssl.type ?? '').toUpperCase()}</Badge>
      <Badge variant="info">#{ssl.id}</Badge>
      <TooltipProvider>
        {sslDomains.map((domain) => {
          const truncated = domain.length > 20;
          const label = truncated ? `${domain.slice(0, 20)}...` : domain;
          return truncated ? (
            <Tooltip key={domain}>
              <TooltipTrigger asChild>
                <Badge variant="outline" className="cursor-default">
                  {label}
                </Badge>
              </TooltipTrigger>
              <TooltipContent>{domain}</TooltipContent>
            </Tooltip>
          ) : (
            <Badge key={domain} variant="outline">
              {label}
            </Badge>
          );
        })}
      </TooltipProvider>
    </div>
  );
}

type Page = {
  primaryDomains: InertiaTableData;
};

export default function PrimaryDomains() {
  const page = usePage<Page>();

  return (
    <Layout secondNavTitle={pluginNavTitle} secondNavItems={pluginNavItems()}>
      <Head title="Primary Domains" />

      <Container className="max-w-6xl">
        <HeaderContainer>
          <Heading
            title="Primary Domains"
            description="Primary domains across every site on every server in the current project."
          />
        </HeaderContainer>

        <VitoTable
          tableData={page.props.primaryDomains}
          cellRenderers={{
            domain: DomainCell,
            certificate: CertificateCell,
            fix_ssl: (props: CellRenderProps) => <FixSslCell {...props} />,
          }}
        />
      </Container>
    </Layout>
  );
}
