import type { CellRenderProps } from '@forjedio/inertia-table-react';

function domainUrl(domain: string): string {
  return domain.startsWith('http://') || domain.startsWith('https://') ? domain : `https://${domain}`;
}

export function DomainCell({ value }: CellRenderProps) {
  const domain = value != null ? String(value) : null;

  if (!domain) {
    return <span className="text-muted-foreground">-</span>;
  }

  return (
    <a href={domainUrl(domain)} target="_blank" rel="noopener noreferrer" className="hover:underline">
      {domain}
    </a>
  );
}
