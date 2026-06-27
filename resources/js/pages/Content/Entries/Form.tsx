import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { MediaPicker } from '@/components/MediaPicker';

export default function EntryForm({ collection, blueprint, entry, supportedLocales }: any) {
    const isEdit = !!entry;
    
    // Initialize data structure safely
    const initialData = entry?.data || {};
    const [activeLocale, setActiveLocale] = useState(supportedLocales[0]);

    // Build the form data skeleton based on the blueprint to ensure fields exist
    const defaultData: any = { global: {} };
    supportedLocales.forEach((locale: string) => {
        defaultData[locale] = {};
    });

    blueprint.fields?.forEach((field: any) => {
        if (field.is_translatable) {
            supportedLocales.forEach((locale: string) => {
                defaultData[locale][field.handle] = initialData[locale]?.[field.handle] ?? '';
            });
        } else {
            defaultData.global[field.handle] = initialData.global?.[field.handle] ?? '';
        }
    });

    const { data, setData, post, put, processing, errors } = useForm({
        status: entry?.status || 'draft',
        blueprint_id: blueprint.id,
        data: defaultData,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit) {
            put(`/admin/content/entries/${entry.id}`);
        } else {
            post(`/admin/content/collections/${collection.id}/entries`);
        }
    };

    const updateField = (handle: string, value: any, isTranslatable: boolean, locale?: string) => {
        if (isTranslatable && locale) {
            setData('data', {
                ...data.data,
                [locale]: {
                    ...data.data[locale],
                    [handle]: value
                }
            });
        } else {
            setData('data', {
                ...data.data,
                global: {
                    ...data.data.global,
                    [handle]: value
                }
            });
        }
    };

    const renderField = (field: any, isTranslatable: boolean, locale?: string) => {
        const value = isTranslatable && locale
            ? data.data[locale]?.[field.handle]
            : data.data.global?.[field.handle];

        const onChange = (val: any) => updateField(field.handle, val, isTranslatable, locale);

        switch (field.type) {
            case 'textarea':
                return (
                    <div key={field.id} className="space-y-2 mb-4">
                        <Label>{field.name}</Label>
                        <Textarea value={value || ''} onChange={(e) => onChange(e.target.value)} rows={5} />
                    </div>
                );
            case 'boolean':
                return (
                    <div key={field.id} className="flex items-center justify-between p-4 border rounded-md mb-4">
                        <div className="space-y-0.5">
                            <Label>{field.name}</Label>
                            <p className="text-sm text-muted-foreground">{field.handle}</p>
                        </div>
                        <Switch checked={!!value} onCheckedChange={onChange} />
                    </div>
                );
            case 'media':
                return (
                    <div key={field.id} className="space-y-2 mb-4">
                        <Label>{field.name}</Label>
                        <MediaPicker value={value || null} onChange={onChange} />
                    </div>
                );
            case 'number':
                return (
                    <div key={field.id} className="space-y-2 mb-4">
                        <Label>{field.name}</Label>
                        <Input type="number" value={value ?? ''} onChange={(e) => onChange(Number(e.target.value))} />
                    </div>
                );
            case 'text':
            default:
                return (
                    <div key={field.id} className="space-y-2 mb-4">
                        <Label>{field.name}</Label>
                        <Input type="text" value={value || ''} onChange={(e) => onChange(e.target.value)} />
                    </div>
                );
        }
    };

    const translatableFields = blueprint.fields?.filter((f: any) => f.is_translatable) || [];
    const globalFields = blueprint.fields?.filter((f: any) => !f.is_translatable) || [];

    const breadcrumbs = [
        { title: 'Content', href: '#' },
        { title: collection.name, href: `/admin/content/collections/${collection.id}/entries` },
        { title: isEdit ? 'Edit Entry' : 'Create Entry', href: '#' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${isEdit ? 'Edit' : 'Create'} ${collection.name} | Content`} />
            <form onSubmit={submit} className="flex flex-col md:flex-row gap-6 p-6">
                
                {/* Main Content Area */}
                <div className="flex-1 space-y-6">
                    <div className="flex items-center justify-between">
                        <h1 className="text-2xl font-bold tracking-tight">
                            {isEdit ? 'Edit Entry' : 'Create New Entry'}
                        </h1>
                    </div>

                    {/* Translatable Fields inside Locale Tabs */}
                    {translatableFields.length > 0 && (
                        <div className="rounded-md border bg-card text-card-foreground shadow-sm">
                            <Tabs value={activeLocale} onValueChange={setActiveLocale} className="w-full">
                                <div className="border-b px-4 pt-4">
                                    <TabsList>
                                        {supportedLocales.map((locale: string) => (
                                            <TabsTrigger key={locale} value={locale} className="uppercase">
                                                {locale}
                                            </TabsTrigger>
                                        ))}
                                    </TabsList>
                                </div>
                                
                                {supportedLocales.map((locale: string) => (
                                    <TabsContent key={locale} value={locale} className="p-6">
                                        {translatableFields.map((field: any) => renderField(field, true, locale))}
                                    </TabsContent>
                                ))}
                            </Tabs>
                        </div>
                    )}

                    {/* Global Fields */}
                    {globalFields.length > 0 && (
                        <div className="rounded-md border bg-card text-card-foreground shadow-sm p-6 mt-6">
                            <h3 className="text-lg font-semibold mb-4">Global Fields (Not Translatable)</h3>
                            {globalFields.map((field: any) => renderField(field, false))}
                        </div>
                    )}
                </div>

                {/* Sidebar / Meta Settings */}
                <div className="w-full md:w-80 space-y-6">
                    <div className="rounded-md border bg-card text-card-foreground shadow-sm p-6 space-y-4">
                        <h3 className="font-semibold text-lg">Publishing</h3>
                        
                        <div className="space-y-2">
                            <Label>Status</Label>
                            <select 
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                value={data.status}
                                onChange={(e) => setData('status', e.target.value)}
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>

                        <div className="pt-4 border-t">
                            <Button type="submit" className="w-full" disabled={processing}>
                                {processing ? 'Saving...' : (isEdit ? 'Save Changes' : 'Create Entry')}
                            </Button>
                        </div>
                    </div>
                </div>

            </form>
        </AppLayout>
    );
}
