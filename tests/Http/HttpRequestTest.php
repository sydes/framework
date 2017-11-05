<?php

namespace Sydes\Tests\Http;

use PHPUnit\Framework\TestCase;
use Sydes\Http\Request;
use Zend\Diactoros\UploadedFile;
use Zend\Diactoros\Uri;

class HttpRequestTest extends TestCase
{
    public function testMethodOverride()
    {
        $request = (new Request)->withMethod('POST')->withQueryParams(['_method' => 'DELETE']);

        $this->assertSame('DELETE', $request->method());
        $this->assertSame('POST', $request->getRealMethod());
    }

    public function testMethodOverrideByQuery()
    {
        $request = (new Request)->withMethod('POST')->withQueryParams(['_method' => 'PUT']);

        $this->assertTrue($request->isMethod('put'));
    }

    public function testMethodOverrideByBody()
    {
        $request = (new Request)->withMethod('POST')->withParsedBody(['_method' => 'PUT']);

        $this->assertTrue($request->isMethod('put'));
    }

    public function testMethodOverrideByHeader()
    {
        $request = (new Request)->withMethod('POST')->withHeader('X-http-METHOD-override', 'put');

        $this->assertTrue($request->isMethod('put'));
    }

    public function testUrl()
    {
        $request = (new Request)->withUri(new Uri('http://domain.tld/baz?a=b'))->withQueryParams(['a' => 'b']);

        $this->assertInternalType('string', $request->url());
        $this->assertSame('http://domain.tld/baz', $request->url());
        $this->assertSame('http://domain.tld/baz?a=b', $request->fullUrl());
        $this->assertSame('http://domain.tld/baz?a=b&c=2', $request->fullUrlWithQuery(['c' => '2']));
        $this->assertSame('http://domain.tld/baz?a=1&c=2', $request->fullUrlWithQuery(['a' => '1', 'c' => '2']));
    }

    public function testPath()
    {
        $request = new Request([], [], 'http://domain.tld/baz?foo=bar');
        $this->assertSame('baz', $request->path());

        $request = new Request([], [], 'http://domain.tld');
        $this->assertSame('/', $request->path());
    }

    public function testDecodedPathMethod()
    {
        $request = new Request([], [], '/foo%20bar');

        $this->assertEquals('foo bar', $request->decodedPath());
    }

    public function testPathChecking()
    {
        $request = new Request([], [], 'http://domain.tld/foo/bar?foo=bar');

        $this->assertTrue($request->is('foo*'));
        $this->assertFalse($request->is('bar*'));
        $this->assertTrue($request->is('*bar*'));
        $this->assertTrue($request->is('bar*', 'foo*', 'baz'));

        $request = new Request([], [], 'http://domain.tld');

        $this->assertTrue($request->is('/'));
    }

    public function testAjaxMethod()
    {
        $request = new Request;

        $this->assertFalse($request->ajax());

        $request = new Request([], [], '/', 'POST', 'php://input', ['X-Requested-With' => 'XMLHttpRequest']);

        $this->assertTrue($request->ajax());
        $this->assertFalse($request->withHeader('X-Requested-With', '')->ajax());
    }

    public function testPjaxMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', ['X-PJAX' => 'true']);

        $this->assertTrue($request->pjax());
        $this->assertFalse($request->withHeader('X-PJAX', 'false')->pjax());
        $this->assertFalse($request->withHeader('X-PJAX', '')->pjax());
    }

    public function testHasMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'age' => '', 'city' => null]);

        $this->assertTrue($request->has('name'));
        $this->assertTrue($request->has('age'));
        $this->assertTrue($request->has('city'));
        $this->assertFalse($request->has('foo'));
        $this->assertFalse($request->has('name', 'email'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'email' => 'foo']);

        $this->assertTrue($request->has('name'));
        $this->assertTrue($request->has('name', 'email'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['foo' => ['bar', 'bar']]);

        $this->assertTrue($request->has('foo'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['foo' => '', 'bar' => null]);

        $this->assertTrue($request->has('foo'));
        $this->assertTrue($request->has('bar'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['foo' => ['bar' => null, 'baz' => '']]);

        $this->assertTrue($request->has('foo.bar'));
        $this->assertTrue($request->has('foo.baz'));
    }

    public function testHasAnyMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'age' => '', 'city' => null]);

        $this->assertTrue($request->hasAny('name'));
        $this->assertTrue($request->hasAny('age'));
        $this->assertTrue($request->hasAny('city'));
        $this->assertFalse($request->hasAny('foo'));
        $this->assertTrue($request->hasAny('name', 'email'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'email' => 'foo']);

        $this->assertTrue($request->hasAny('name', 'email'));
        $this->assertFalse($request->hasAny('surname', 'password'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['foo' => ['bar' => null, 'baz' => '']]);

        $this->assertTrue($request->hasAny('foo.bar'));
        $this->assertTrue($request->hasAny('foo.baz'));
        $this->assertFalse($request->hasAny('foo.bax'));
    }

    public function testFilledMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'age' => '', 'city' => null]);

        $this->assertTrue($request->filled('name'));
        $this->assertFalse($request->filled('age'));
        $this->assertFalse($request->filled('city'));
        $this->assertFalse($request->filled('foo'));
        $this->assertFalse($request->filled('name', 'email'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'email' => 'foo']);

        $this->assertTrue($request->filled('name'));
        $this->assertTrue($request->filled('name', 'email'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['foo' => ['bar', 'baz']]);

        $this->assertTrue($request->filled('foo'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['foo' => ['bar' => 'baz']]);

        $this->assertTrue($request->filled('foo.bar'));
    }

    public function testInputMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon']);

        $this->assertEquals('Anon', $request->input('name'));
        $this->assertEquals('Bob', $request->input('foo', 'Bob'));
    }

    public function testAllMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'age' => null]);

        $this->assertEquals(['name' => 'Anon', 'age' => null, 'email' => null], $request->all('name', 'age', 'email'));
        $this->assertEquals(['name' => 'Anon'], $request->all('name'));
        $this->assertEquals(['name' => 'Anon', 'age' => null], $request->all());

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['developer' => ['name' => 'Anon', 'age' => null]]);

        $this->assertEquals(['developer' => ['name' => 'Anon', 'skills' => null]], $request->all('developer.name', 'developer.skills'));
        $this->assertEquals(['developer' => ['name' => 'Anon', 'skills' => null]], $request->all(['developer.name', 'developer.skills']));
        $this->assertEquals(['developer' => ['age' => null]], $request->all('developer.age'));
        $this->assertEquals(['developer' => ['skills' => null]], $request->all('developer.skills'));
        $this->assertEquals(['developer' => ['name' => 'Anon', 'age' => null]], $request->all());
    }

    public function testKeysMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'age' => null]);

        $this->assertEquals(['name', 'age'], $request->keys());

        $files = ['foo' => new UploadedFile(__FILE__, 200, UPLOAD_ERR_OK)];

        $request = new Request([], $files, '/', 'GET');

        $this->assertEquals(['foo'], $request->keys());

        $request = new Request([], $files, '/', 'GET', 'php://input', [], [], ['name' => 'Anon']);

        $this->assertEquals(['name', 'foo'], $request->keys());
    }

    public function testOnlyMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'age' => null]);

        $this->assertEquals(['name' => 'Anon', 'age' => null], $request->only('name', 'age', 'email'));

        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['developer' => ['name' => 'Anon', 'age' => null]]);

        $this->assertEquals(['developer' => ['name' => 'Anon']], $request->only('developer.name', 'developer.skills'));
        $this->assertEquals(['developer' => ['age' => null]], $request->only('developer.age'));
        $this->assertEquals([], $request->only('developer.skills'));
    }

    public function testExceptMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon', 'age' => 25]);

        $this->assertEquals(['name' => 'Anon'], $request->except('age'));
        $this->assertEquals([], $request->except('age', 'name'));
    }

    public function testQueryMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], [], ['name' => 'Anon']);

        $this->assertEquals('Anon', $request->query('name'));
        $this->assertEquals('Bob', $request->query('foo', 'Bob'));

        $all = $request->query(null);

        $this->assertEquals('Anon', $all['name']);
    }

    public function testPostMethod()
    {
        $request = new Request([], [], '/', 'POST', 'php://input', [], [], [], ['name' => 'Anon']);

        $this->assertEquals('Anon', $request->post('name'));
        $this->assertEquals('Bob', $request->post('foo', 'Bob'));

        $all = $request->post(null);

        $this->assertEquals('Anon', $all['name']);
    }

    public function testCookieMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], ['name' => 'Anon']);

        $this->assertEquals('Anon', $request->cookie('name'));
        $this->assertEquals('Bob', $request->cookie('foo', 'Bob'));

        $all = $request->cookie(null);

        $this->assertEquals('Anon', $all['name']);
    }

    public function testHasCookieMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', [], ['foo' => 'bar']);

        $this->assertTrue($request->hasCookie('foo'));
        $this->assertFalse($request->hasCookie('baz'));
    }

    public function testFileMethod()
    {
        $request = new Request([], ['foo' => new UploadedFile(__FILE__, 200, UPLOAD_ERR_OK)], '/', 'GET');

        $this->assertInstanceOf(UploadedFile::class, $request->file('foo'));
    }

    public function testHasFileMethod()
    {
        $request = new Request([], [], '/', 'GET');

        $this->assertFalse($request->hasFile('foo'));

        $request = new Request([], ['foo' => new UploadedFile(__FILE__, 200, UPLOAD_ERR_OK)], '/', 'GET');

        $this->assertTrue($request->hasFile('foo'));
    }

    public function testServerMethod()
    {
        $request = new Request(['foo' => 'bar']);

        $this->assertEquals('bar', $request->server('foo'));
        $this->assertEquals('bar', $request->server('foo.doesnt.exist', 'bar'));

        $all = $request->server(null);

        $this->assertEquals('bar', $all['foo']);
    }

    public function testHeaderMethod()
    {
        $request = new Request([], [], '/', 'GET', 'php://input', ['Do-This' => 'foo']);

        $this->assertEquals('foo', $request->header('do-this'));

        $all = $request->header(null);

        $this->assertEquals('foo', $all['Do-This'][0]);
    }

    public function testPrefers()
    {
        $request = new Request;

        $this->assertEquals('json', $request->withHeader('Accept', 'application/json')->prefers(['json']));
        $this->assertEquals('json', $request->withHeader('Accept', 'application/json')->prefers(['html', 'json']));
        $this->assertEquals('application/foo+json', $request->withHeader('Accept', 'application/foo+json')->prefers('application/foo+json'));
        $this->assertEquals('json', $request->withHeader('Accept', 'application/foo+json')->prefers('json'));
        $this->assertEquals('html', $request->withHeader('Accept', 'application/json;q=0.5, text/html;q=1.0')->prefers(['json', 'html']));
        $this->assertEquals('txt', $request->withHeader('Accept', 'application/json;q=0.5, text/plain;q=1.0, text/html;q=1.0')->prefers(['json', 'txt', 'html']));
        $this->assertEquals('json', $request->withHeader('Accept', 'application/*')->prefers('json'));
        $this->assertEquals('json', $request->withHeader('Accept', 'application/json; charset=utf-8')->prefers('json'));
        $this->assertNull($request->withHeader('Accept', 'application/xml; charset=utf-8')->prefers(['html', 'json']));
        $this->assertEquals('json', $request->withHeader('Accept', 'application/json, text/html')->prefers(['html', 'json']));
        $this->assertEquals('html', $request->withHeader('Accept', 'application/json;q=0.4, text/html;q=0.6')->prefers(['html', 'json']));
        $this->assertEquals('application/json', $request->withHeader('Accept', 'application/json; charset=utf-8')->prefers('application/json'));
        $this->assertEquals('application/json', $request->withHeader('Accept', 'application/json, text/html')->prefers(['text/html', 'application/json']));
        $this->assertEquals('text/html', $request->withHeader('Accept', 'application/json;q=0.4, text/html;q=0.6')->prefers(['text/html', 'application/json']));
        $this->assertEquals('text/html', $request->withHeader('Accept', 'application/json;q=0.4, text/html;q=0.6')->prefers(['application/json', 'text/html']));
        $this->assertEquals('json', $request->withHeader('Accept', '*/*; charset=utf-8')->prefers('json'));
        $this->assertEquals('application/json', $request->withHeader('Accept', 'application/*')->prefers('application/json'));
        $this->assertEquals('application/xml', $request->withHeader('Accept', 'application/*')->prefers('application/xml'));
        $this->assertNull($request->withHeader('Accept', 'application/*')->prefers('text/html'));
    }

    public function testFormatReturnsAcceptableFormat()
    {
        $request = (new Request)->withHeader('Accept', 'application/json');

        $this->assertEquals('json', $request->format());
        $this->assertTrue($request->wantsJson());

        $request = (new Request)->withHeader('Accept', 'application/json; charset=utf-8');

        $this->assertEquals('json', $request->format());
        $this->assertTrue($request->wantsJson());

        $request = (new Request)->withHeader('Accept', 'application/atom+xml');

        $this->assertEquals('atom', $request->format());
        $this->assertFalse($request->wantsJson());

        $request = (new Request)->withHeader('Accept', 'is/not/known');

        $this->assertEquals('html', $request->format());
        $this->assertEquals('foo', $request->format('foo'));
    }

    public function testFormatReturnsAcceptsJson()
    {
        $request = (new Request)->withHeader('Accept', 'application/json');

        $this->assertEquals('json', $request->format());
        $this->assertTrue($request->accepts('application/json'));
        $this->assertTrue($request->accepts('application/baz+json'));
        $this->assertTrue($request->acceptsJson());
        $this->assertFalse($request->acceptsHtml());

        $request = (new Request)->withHeader('Accept', 'application/foo+json');

        $this->assertTrue($request->accepts('application/foo+json'));
        $this->assertFalse($request->accepts('application/bar+json'));
        $this->assertFalse($request->accepts('application/json'));

        $request = (new Request)->withHeader('Accept', 'application/*');

        $this->assertTrue($request->accepts('application/xml'));
        $this->assertTrue($request->accepts('application/json'));
    }

    public function testFormatReturnsAcceptsHtml()
    {
        $request = (new Request)->withHeader('Accept', 'text/html');

        $this->assertEquals('html', $request->format());
        $this->assertTrue($request->accepts('text/html'));
        $this->assertTrue($request->acceptsHtml());
        $this->assertFalse($request->acceptsJson());

        $request = (new Request)->withHeader('Accept', 'text/*');

        $this->assertTrue($request->accepts('text/html'));
        $this->assertTrue($request->accepts('text/plain'));
    }

    public function testFormatReturnsAcceptsAll()
    {
        $request = (new Request)->withHeader('Accept', '*/*');

        $this->assertEquals('html', $request->format());
        $this->assertTrue($request->accepts('text/html'));
        $this->assertTrue($request->accepts('foo/bar'));
        $this->assertTrue($request->accepts('application/baz+xml'));
        $this->assertTrue($request->acceptsHtml());
        $this->assertTrue($request->acceptsJson());

        $request = (new Request)->withHeader('Accept', '*');

        $this->assertEquals('html', $request->format());
        $this->assertTrue($request->accepts('text/html'));
        $this->assertTrue($request->accepts('foo/bar'));
        $this->assertTrue($request->accepts('application/baz+xml'));
        $this->assertTrue($request->acceptsHtml());
        $this->assertTrue($request->acceptsJson());
    }

    public function testFormatReturnsAcceptsMultiple()
    {
        $request = (new Request)->withHeader('Accept', 'application/json,text/*');

        $this->assertTrue($request->accepts(['text/html', 'application/json']));
        $this->assertTrue($request->accepts('text/html'));
        $this->assertTrue($request->accepts('text/foo'));
        $this->assertTrue($request->accepts('application/json'));
        $this->assertTrue($request->accepts('application/baz+json'));
    }

    public function testFormatReturnsAcceptsCharset()
    {
        $request = (new Request)->withHeader('Accept', 'application/json; charset=utf-8');

        $this->assertTrue($request->accepts(['text/html', 'application/json']));
        $this->assertFalse($request->accepts('text/html'));
        $this->assertFalse($request->accepts('text/foo'));
        $this->assertTrue($request->accepts('application/json'));
        $this->assertTrue($request->accepts('application/baz+json'));
    }

    public function testBadAcceptHeader()
    {
        $request = (new Request)->withHeader('Accept', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-PT; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)');

        $this->assertFalse($request->accepts(['text/html', 'application/json']));
        $this->assertFalse($request->accepts('text/html'));
        $this->assertFalse($request->accepts('text/foo'));
        $this->assertFalse($request->accepts('application/json'));
        $this->assertFalse($request->accepts('application/baz+json'));
        $this->assertFalse($request->acceptsHtml());
        $this->assertFalse($request->acceptsJson());

        // Should not be handled as regex.
        $request = (new Request)->withHeader('Accept', '.+/.+');

        $this->assertFalse($request->accepts('application/json'));
        $this->assertFalse($request->accepts('application/baz+json'));

        // Should not produce compilation error on invalid regex.
        $request = (new Request)->withHeader('Accept', '(/(');

        $this->assertFalse($request->accepts('text/html'));
    }
}
