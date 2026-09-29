/**
 * krubot $nano-net - Ultra-lightweight, Modern Web Polyfill for the Legendary jQuery.Ajax
 * The Performant Vanilla JS Engine, Built to blaze in both: High Performance && Ultra-DX.
 * No jQuery, No TypeScript, No Build Pipeline, Zero Dependencies.
 * @see https://StoryKo.de/Krubot Official website of engine.
 * @link https://github.com/StoryKode/Krubot Main Repo of Krubot Bot Matrix.
 * @version 4.0
*/
(function (root, factory) {
	const api = factory();
	if (typeof module == 'object' && module.exports) module.exports = api;
	else root.$nn = api;
})(globalThis, function () {
	'use strict';
	
	const defaults = { baseUrl: '', headers: {} };
	const fn = x => typeof x == 'function';
	const plain = x => x && typeof x == 'object' &&
		(Object.getPrototypeOf(x) === Object.prototype || Object.getPrototypeOf(x) === null);
	
	const body = x => x && (
		(typeof FormData != 'undefined' && x instanceof FormData) ||
		(typeof Blob != 'undefined' && x instanceof Blob) ||
		(typeof URLSearchParams != 'undefined' && x instanceof URLSearchParams) ||
		(typeof ArrayBuffer != 'undefined' && (x instanceof ArrayBuffer || ArrayBuffer.isView(x)))
	);
	
	const has = (h, n) => Object.keys(h).some(k => k.toLowerCase() == n.toLowerCase());
	const addQuery = (u, q) => q ? u + (u.includes('?') ? (/[?&]$/.test(u) ? '' : '&') : '?') + q : u;

	const param = (obj, traditional = false) => {
		if (obj == null) return '';
		if (typeof obj == 'string' || obj instanceof URLSearchParams) return String(obj);
		const out = [], put = (k, v) => {
			v = fn(v) ? v() : (v == null ? '' : v);
			out.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
		};
		const walk = (k, v) => {
			if (Array.isArray(v)) v.forEach((x, i) =>
				traditional || /\[\]$/.test(k) ? put(k, x) : walk(k + '[' + (plain(x) || Array.isArray(x) ? i : '') + ']', x));
			else if (!traditional && plain(v)) Object.keys(v).forEach(x => walk(k + '[' + x + ']', v[x]));
			else put(k, v);
		};
		if (Array.isArray(obj)) obj.forEach(x => x && 'name' in x && put(x.name, x.value));
		else if (plain(obj)) Object.keys(obj).forEach(k => walk(k, obj[k]));
		else put('', obj);
		return out.join('&').replace(/%20/g, '+');
	};

	const typeOf = (asked, ct) => {
		if (asked) return String(asked).toLowerCase().split(/\s/)[0].replace(/^data-/, '');
		ct = (ct || '').toLowerCase();
		return ct.includes('json') ? 'json' : ct.includes('xml') ? 'xml' :
			ct.includes('html') ? 'html' : ct.includes('javascript') ? 'script' : 'text';
	};

	async function read(res, asked, jq, executeScript = true) {
		const type = typeOf(asked, res.headers.get('content-type'));
		
		if (type == 'blob') return (jq.response = await res.blob());
		if (type == 'arraybuffer') return (jq.response = await res.arrayBuffer());
		
		const text = await res.text();
		jq.responseText = text;
		
		if (type == 'json') {
			const t = text.trim();
			jq.responseJSON = t ? JSON.parse(t) : null;
			return (jq.response = jq.responseJSON);
		}
		if (type == 'xml') {
			const xml = new DOMParser().parseFromString(text, 'text/xml');
			if (xml.querySelector?.('parsererror')) throw SyntaxError('Invalid XML');
			jq.responseXML = xml;
			return (jq.response = xml);
		}
		if (type == 'script') {
			if (executeScript) (0, eval)(text);
			return (jq.response = text);
		}
		return (jq.response = text);
	}

	const callStatus = (s, ctx, code, ...args) => {
		const cb = s.statusCode?.[code];
		if (fn(cb)) cb.apply(ctx, args);
	};

	function ajax(options) {
		if (typeof options == 'string') options = { url: options };
		const s = { ...defaults, ...options },
			headers = { ...defaults.headers, ...s.headers },
			ctx = s.context || s,
			method = (s.method || s.type || 'GET').toUpperCase(),
			noBody = method == 'GET' || method == 'HEAD';
			
		/// let url = s.url || '', data = s.data, timer, timedOut = false, stopped = false;
		let url = (s.baseUrl && !/^https?:\/\//i.test(s.url || '')) ?
			(
				s.baseUrl.replace(/\/+$/, '') + '/' + (s.url || '').replace(/^\/+/, '')
			)
		:
			(s.url || '');

		let data = s.data, timer, timedOut = false, stopped = false;
		
		if (data != null && !body(data) && typeof data != 'string' && s.processData !== false)
			data = param(data, s.traditional);
			
		if (noBody && data != null) { url = addQuery(url, data); data = undefined; }
		if (s.cache === false && noBody) url = addQuery(url, '_=' + Date.now());
		if (data != null && !noBody && !body(data) && s.contentType !== false && !has(headers, 'Content-Type'))
			headers['Content-Type'] = s.contentType || 'application/x-www-form-urlencoded; charset=UTF-8';

		const ac = new AbortController(), ext = s.signal;
		const stop = reason => { stopped = reason != 'timeout'; ac.abort(reason); };
		if (ext?.aborted) stop(ext.reason || 'abort');
		else ext?.addEventListener('abort', () => stop(ext.reason || 'abort'), { once: true });

		const jq = { 
			readyState: 1, status: 0, statusText: '', response: null, 
			responseText: '', responseJSON: undefined, responseXML: null, abort: stop 
		};

		const p = new Promise((resolve, reject) => {
			if (s.beforeSend?.call(ctx, jq, s) === false) {
				jq.statusText = 'canceled';
				s.error?.call(ctx, jq, 'canceled', jq);
				s.complete?.call(ctx, jq, 'canceled');
				return reject(jq);
			}
			if (s.timeout > 0) timer = setTimeout(() => { timedOut = true; stop('timeout'); }, +s.timeout);
			
			fetch(url, {
				method, headers, body: noBody ? undefined : data, signal: ac.signal,
				credentials: s.credentials, mode: s.mode, redirect: s.redirect,
				cache: s.cache === false ? 'no-store' : undefined
			}).then(async res => {
				jq.readyState = 4;
				jq.status = res.status;
				jq.getResponseHeader = n => res.headers.get(n);
				jq.getAllResponseHeaders = () => {
					const out = [];
					res.headers.forEach((v, k) => out.push(`${k}: ${v}`));
					return out.join('\r\n');
				};

				await read(res, s.dataType, jq, s.executeScript !== false);

				if (!res.ok) {
					const e = Object.assign(Error(res.statusText || 'HTTP ' + res.status), { status: res.status, response: jq.response });
					callStatus(s, ctx, res.status, jq, 'error', e);
					throw e;
				}

				jq.statusText = res.statusText || 'OK';
				s.success?.call(ctx, jq.response, jq.statusText, jq);
				callStatus(s, ctx, res.status, jq, 'success', jq.response);
				resolve(jq.response);
				
			}).catch(err => {
				jq.statusText = stopped ? 'abort' : (timedOut ? 'timeout' : 'error');
				s.error?.call(ctx, jq, jq.statusText, err);
				reject(err);
			}).finally(() => {
				if (timer) clearTimeout(timer);
				s.complete?.call(ctx, jq, jq.statusText);
			});
		});

		p.abort = jq.abort;
		return p;
	}

	const strike = (type, url, data, success, dataType) => {
		if (fn(data)) {
			dataType = dataType || success;
			success = data;
			data = undefined;
		}
		return ajax({ type, url, data, success, dataType });
	};

	return {
		strike: ajax, ajax, param,
		ajaxSetup: x => {
			if (!x) return defaults;
			if (x.headers) defaults.headers = { ...defaults.headers, ...x.headers };
			for (const k of Object.keys(x)) if (k !== 'headers') defaults[k] = x[k];
			return defaults;
		},
		baseUrl: url => url !== undefined ? (defaults.baseUrl = url, defaults.baseUrl) : defaults.baseUrl,
		get: (url, data, listener, dtype) => strike('GET', url, data, listener, dtype),
		post: (url, data, listener, dtype) => strike('POST', url, data, listener, dtype),
		getJSON: (url, data, listener) => strike('GET', url, data, listener, 'json'),
		getScript: (url, listener) => strike('GET', url, undefined, listener, 'script'),
		version: '4.0.0'
	};
});
