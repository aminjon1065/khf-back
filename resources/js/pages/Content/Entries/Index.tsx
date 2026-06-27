import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';

export default function EntryIndex({ collection, entries }: { collection: any, entries: any }) {
    const breadcrumbs = [
        { title: 'Content', href: '#' },
        { title: collection.name, href: `/admin/content/collections/${collection.id}/entries` },
    ];

    const deleteEntry = (id: string) => {
        if (confirm('Are you sure you want to delete this entry?')) {
            router.delete(`/admin/content/entries/${id}`);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${collection.name} | Content`} />
            <div className="flex flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">{collection.name}</h1>
                        <p className="text-muted-foreground">Manage entries for this collection.</p>
                    </div>
                    <Link
                        href={`/admin/content/collections/${collection.id}/entries/create`}
                        className="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90"
                    >
                        Create Entry
                    </Link>
                </div>

                <div className="rounded-md border">
                    <div className="relative w-full overflow-auto">
                        <table className="w-full caption-bottom text-sm">
                            <thead className="[&_tr]:border-b">
                                <tr className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Title</th>
                                    <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                                    <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Author</th>
                                    <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Date</th>
                                    <th className="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="[&_tr:last-child]:border-0">
                                {entries.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="p-4 text-center text-muted-foreground">
                                            No entries found.
                                        </td>
                                    </tr>
                                ) : (
                                    entries.data.map((entry: any) => (
                                        <tr key={entry.id} className="border-b transition-colors hover:bg-muted/50">
                                            <td className="p-4 align-middle font-medium">
                                                {entry.data?.tg?.title || entry.data?.global?.title || entry.slug || 'Untitled'}
                                            </td>
                                            <td className="p-4 align-middle">
                                                <span className={`inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold ${entry.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                                                    {entry.status}
                                                </span>
                                            </td>
                                            <td className="p-4 align-middle">{entry.author?.name || 'System'}</td>
                                            <td className="p-4 align-middle">
                                                {entry.published_at ? new Date(entry.published_at).toLocaleDateString() : '-'}
                                            </td>
                                            <td className="p-4 align-middle text-right space-x-2">
                                                <Link
                                                    href={`/admin/content/entries/${entry.id}/edit`}
                                                    className="inline-flex h-8 items-center justify-center rounded-md border px-3 text-sm font-medium hover:bg-muted"
                                                >
                                                    Edit
                                                </Link>
                                                <Button variant="destructive" size="sm" onClick={() => deleteEntry(entry.id)}>
                                                    Delete
                                                </Button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
