import { cn } from '@/lib/utils';

const LABELS: Record<string, string> = {
    tg: 'Тоҷикӣ',
    ru: 'Русский',
    en: 'English',
};

export function LocaleTabs({
    locales,
    active,
    onChange,
}: {
    locales: string[];
    active: string;
    onChange: (locale: string) => void;
}) {
    return (
        <div className="inline-flex rounded-lg border bg-muted p-0.5">
            {locales.map((locale) => (
                <button
                    key={locale}
                    type="button"
                    onClick={() => onChange(locale)}
                    className={cn(
                        'rounded-md px-3 py-1 text-sm font-medium transition-colors',
                        active === locale
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground',
                    )}
                >
                    {LABELS[locale] ?? locale}
                </button>
            ))}
        </div>
    );
}
