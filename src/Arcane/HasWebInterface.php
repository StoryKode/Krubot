<?php

namespace KrubiK\Arcane;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.9×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\View\View;
use JsonSerializable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as LaravelResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Contracts\Support\Responsable;

trait HasWebInterface
{

    /**
     * The final, generated HTTP response for the current lifecycle.
     * This acts as the state container for the response, allowing any middleware
     * or the final handler to set it. It decouples response *generation*
     * from response *sending*.
     *
     * @var \Symfony\Component\HttpFoundation\Response|null
     */
    protected ?SymfonyResponse $finalResponse = null;

    /**
     * Normalizes any return payload from controllers or web actions into a proper HTTP Response.
     * Supported: Views, Htmlables, Stringables, Strings, Arrays, Arrayables, Jsonables, Responsables, and raw Objects.
     *
     * @param mixed $result
     * @return SymfonyResponse
    */
    protected function toHttpResponse(mixed $result): SymfonyResponse
    {
        // 1. INSTANT PASS-THROUGH: If it is already a Symfony/Laravel Response (or RedirectResponse), return it.
        if ($result instanceof SymfonyResponse) {
            return $result;
        }

         // 2. SECOND PASS-THROUGH: If it is already a Laravel Responsable return it directly.
        if ($result instanceof Responsable) {
            return $result->toResponse(request());
        }

        // 3. EMPTY RESPONSES: Treat null returning functions as "204 No Content".
        if ($result === null) {
            return new LaravelResponse('', 204);
        }

        // 4. HTML & RENDERABLES: Render to string and output as HTML with UTF-8 charset.
        if (
            $result instanceof View ||
            $result instanceof Htmlable ||
            $result instanceof \Stringable ||
            is_string($result)
        ) {
            $html = match (true) {
                $result instanceof View       => $result->render(),
                $result instanceof Htmlable   => $result->toHtml(),
                default                       => (string) $result,
            };

            return new LaravelResponse($html, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
            ]);
        }

        // 5. JSON & SERIALIZABLES: Check structure and return as JsonResponse.
        if (
            is_array($result) ||
            $result instanceof Arrayable ||
            $result instanceof Jsonable ||
            $result instanceof JsonSerializable ||
            $result instanceof \stdClass
        ) {
            $data = match (true) {
                $result instanceof Arrayable        => $result->toArray(),
                $result instanceof JsonSerializable => $result->jsonSerialize(),
                default                             => $result,
            };

            return new JsonResponse($data, 200);
        }

        // 6. OBJECT & SCALAR FALLBACKS: Handle fallback scenarios gracefully.
        if (is_object($result)) {
            // Fallback for custom objects containing a __toString magic method.
            if (method_exists($result, '__toString')) {
                return new LaravelResponse((string) $result, 200, [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ]);
            }

            // Try to JSON-serialize foreign objects, if that fails, output raw debug text.
            try {
                return new JsonResponse($result, 200);
            } catch (\Throwable) {
                return new LaravelResponse(print_r($result, true), 200, [
                    'Content-Type' => 'text/plain; charset=UTF-8',
                ]);
            }
        }

        // 7. Scalar fallback (integers, floats, booleans)
        return new LaravelResponse((string) $result, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Sets and normalizes the final HTTP response for the current request lifecycle.
     * This provides a fluent interface for overriding or setting the response from anywhere,
     * such as a middleware that needs to short-circuit the request (e.g., for auth).
     *
     * @param mixed $content The content to be converted into a response.
     * @return self Returns the instance for method chaining.
    */
    public function setResponse(mixed $content): self
    {
        $this->finalResponse = $this->toHttpResponse($content);
        return $this;
    }

    /**
     * Retrieves the generated Symfony Response object, if one has been set.
     * This is the canonical way to get the final response before sending it.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
    */
    public function getResponse(): ?SymfonyResponse
    {
        return $this->finalResponse;
    }

    /**
     * Checks if a response has been generated and is ready to be sent.
     *
     * @return bool
    */
    public function hasResponse(): bool
    {
        return $this->finalResponse !== null;
    }

    /**
     * A powerful, jQuery-style dual-purpose method to get or set the final response.
     * This implementation uses PHP's native argument counting for maximum simplicity and elegance.
     *
     * A powerful alias for setResponse, often used to signal the final action.
     * - As a SETTER: `$bot->response('Page content')` -> Normalizes and sets the response. Returns `$this`.
     * - As a GETTER: `$bot->response()` -> Returns the generated `Symfony\Component\HttpFoundation\Response` object.
     *
     * @return self|\Symfony\Component\HttpFoundation\Response|null
    */
    public function response(): self|SymfonyResponse|null
    {
        // GETTER MODE: The method was called without any arguments.
        if (func_num_args() === 0) {
            return $this->finalResponse;
        }

        // SETTER MODE: The method was called with at least one argument.
        // We retrieve the first argument passed to the function.
        $this->finalResponse = $this->toHttpResponse(func_get_arg(0));  /// $content = func_get_arg(0);
        
        return $this;
    }

}
